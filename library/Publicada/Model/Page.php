<?php

class Publicada_Model_Page extends Zend_Db_Table_Row_Abstract
{
	const DEFAULT_ROOT_PAGE_CODE	= 'root';
	const FILE_TYPE_IMAGE			= 1;
	
	protected $_publishedChildren 	= null;
	protected $_parent				= null;
	
	/**
	 * Get published child pages
	 * 
	 * @return	Zend_Db_Table_Rowset	$publishedChildPages	Resulting rowset
	 */
	public function getPublishedChildren()
	{
		if (null === $this->_publishedChildren) {
			$childrenSelect = $this->select()
								   ->where('parent_code = ?', $this->code)
								   ->where('language = ?', Zend_Registry::get('content_locale'))
								   ->where('status = ?', Publicada_Model_Pages::STATUS_PUBLISHED);
			
			$this->_publishedChildren = $this->getTable()->fetchAll( $childrenSelect);
		}
		
		return $this->_publishedChildren;
	}
	
	public function getParent()
	{
		if (null === $this->_parent && !empty($this->parent_code)) {
			$this->_parent = $this->getTable()->fetchRow($this->select()->where('code = ?', $this->parent_code));
		}
		
		return $this->_parent;
	}

	public function setFromArray(array $data)
	{
		$date = new Zend_Date();
		$now = $date->toString('YYYY-MM-dd HH:mm:ss');
		
		if (null === $this->_data['created_at']) {
			$data['created_at'] = $now;
		}
		
		if (null === $this->_data['published_at']) {
			$data['published_at'] = $now;
		}
		
		$data['updated_at'] = $now;

		parent::setFromArray($data);
	}
	
	protected function _insert()
	{
		$this->_rootPageHasEmptyParentCode();
	}
	
	protected function _update()
	{
		$this->_rootPageHasEmptyParentCode();
	}
	
	protected function _postInsert() {
		// add to index
		$index = $this->getTable()->getIndex();
		$index->addDocument(new Publicada_Search_IndexedPage($this));
		$index->commit();
		
		$this->clearCache();
	}
	
	protected function _postUpdate()
	{
		if ($this->code != $this->_cleanData['code']) {
			$childPages = $this->getTable()->fetchAll($this->select()->where('parent_code = ?', $this->_cleanData['code']));
			foreach ($childPages as $childPage) {
				$childPage->parent_code = $this->code;
				$childPage->save();
			}
		}

		// replace in index
		$index = $this->getTable()->getIndex();
		$term = new Zend_Search_Lucene_Index_Term($this->id, 'page_id');
		foreach ($index->termDocs($term) as $match) {
        	$index->delete($match);
		}
		$index->commit();
		$index->addDocument(new Publicada_Search_IndexedPage($this));
		$index->commit();
		
		$this->clearCache();
	}
	
	protected function _postDelete() {
		// remove in index
		$index = $this->getTable()->getIndex();
		$term = new Zend_Search_Lucene_Index_Term($this->id, 'page_id');
		foreach ($index->termDocs($term) as $match) {
        	$index->delete($match);
		}
		$index->commit();
		
		$this->clearCache();
	}
	
	protected function _rootPageHasEmptyParentCode()
	{
		/**
		 * Made parent code null for page with 'root' code
		 */
		if ($this->_data['code'] == self::DEFAULT_ROOT_PAGE_CODE) {
			$this->_data['parent_code'] = null;
		}
	}

	public function getImages()
	{
		$imageSelect = $this->select()->where('type = ?', self::FILE_TYPE_IMAGE);
		$images = $this->findManyToManyRowset('Publicada_Model_Files',
											 'Publicada_Model_Pages2Files',
											 null,
											 null,
											 $imageSelect);
		return $images;
	}
	
	public function getImage()
	{
		$images = $this->getImages();
		
		return $images->current();
	}
	
	public function clearCache() {
		$cacheConfig = new Zend_Config(require APPLICATION_PATH . '/configs/cache.php');
		$frontendOptions = $cacheConfig->frontendOptions->toArray();
		$backendOptions  = $cacheConfig->backendOptions->toArray();

        $cache = Zend_Cache::factory('Page',
                                     'File',
                                     $frontendOptions,
                                     $backendOptions);
        $cache->clean(
			Zend_Cache::CLEANING_MODE_MATCHING_TAG,
			array('pages')
		);
	}
}
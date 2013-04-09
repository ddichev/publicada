<?php

class Publicada_Model_Source extends Zend_Db_Table_Row_Abstract
{
    protected $_categories = array();

	protected function _getCategories()
	{
	}

	public function setFromArray(array $data)
	{
		$date = new Zend_Date();
		$now = $date->toString('YYYY-MM-dd HH:mm:ss');
		$data['updated_at'] = $now;

		parent::setFromArray($data);
	}
	
	public function init()
	{
	}
	
	protected function _updateCategories()
	{
	}
	
//	protected function _postUpdate()
//	{
//		$this->_updateCategories();
//		$index = $this->getTable()->getIndex();
//		$term = new Zend_Search_Lucene_Index_Term($this->id, 'ad_id');
//		foreach ($index->termDocs($term) as $match) {
//        	$index->delete($match);
//			$index->commit();
//		}
//		$index->addDocument(new Publicada_Search_IndexedAd($this));
//		$index->commit();
//	}
	
//	protected function _postInsert()
//	{
//		$this->_updateCategories();
//		$index = $this->getTable()->getIndex();
//		$index->addDocument(new Publicada_Search_IndexedAd($this));
//		$index->commit();
//	}
	
//	protected function _postDelete()
//	{
//		$index = $this->getTable()->getIndex();
//		$term = new Zend_Search_Lucene_Index_Term($this->id, 'ad_id');
//		foreach ($index->termDocs($term) as $match) {
//        	$index->delete($match);
//		}
//		$index->commit();
//	}
	
	protected function _postInsert()
	{
		$this->clearCache();
	}
	
	protected function _postUpdate()
	{
		$this->clearCache();
	}
	
	protected function _postDelete()
	{
		$this->clearCache();
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
			array('sources')
		);
	}
}
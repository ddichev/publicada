<?php

class Publicada_Model_Post extends Zend_Db_Table_Row_Abstract
{
	const FILE_TYPE_IMAGE = 1;

    /**
     * The IDs of the categories attached to this object
     *
     * @var array
     */
    protected $_categories = array();

	/**
	 * Get rowset of categories related to post
	 *
	 * @return	Zend_Db_Table_Rowset	$publishedPosts	Resulting paginator of rows
	 */
	private function _getCategories()
	{
		$post2CategorySelect = $this->getTable()->getAdapter()->select();
		$post2CategorySelect->from(array('p2pc' => 'posts2post_categories'))
						   ->where('p2pc.post_id = ?', $this->id)
						   ->join(
						      array('c' => 'categories'),
						      'c.id = p2pc.post_category_id'
						   );
						   
		$posts2Categories = $this->getTable()->getAdapter()->fetchAll($post2CategorySelect);
			
		return $posts2Categories;
	}
	

	public function getCategories() {
		return $this->_getCategories();
	}

	/**
	 * 
	 * @param	object					$limit[optional]
	 * @param	object					$page[optional]
	 * @return	Zend_Db_Table_Rowset	$similarPostsRowset
	 * 
	 */
	public function getSimilarPosts($limit = 8, $page = 1, $options = array())
	{
		$categories = $this->_getCategories();
		
		$postSelect = $this->getTable()->getAdapter()->select();
		$postSelect->from(array('p' => 'posts'));
		
		$postSelect->where('p.language = ?', Zend_Registry::get('content_locale'))
				   ->where('p.status = ?', Publicada_Model_Posts::STATUS_PUBLISHED)
				   ->where('p.id <> ?', $this->id)
				   ->order('p.published_at DESC');
				   		
		$postSelect->join(
			array('p2pc' => 'posts2post_categories'),
			'p2pc.post_id = p.id',
			array(
				'title'		=> 'p.title', 
				'language'	=> 'p.language',
				'id'		=> 'p.id',
				'body'		=> 'p.body'
			)
		);
		
		
		if (count($categories) > 1) {
			$multipleCategoriesConditions = array();
			$dbAdapter = $this->getTable()->getAdapter();
			
			foreach ($categories as $category) {
				$multipleCategoriesConditions[] = $dbAdapter->quoteInto('p2pc.post_category_id = ?',
																		$category->post_category_id);
			}
			$postSelect->where(implode(' OR ', $multipleCategoriesConditions));
		}
		elseif (count($categories) == 1) {
			$category = $categories[0];
			$postSelect->where('p2pc.post_category_id = ?', $category->post_category_id);
		}
		
		$postSelect->group('p.id');
		
		$paginator = Zend_Paginator::factory($postSelect);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($limit);
		
		return $paginator;
	}
	
	/**
	 * 
	 * @return 
	 * @param object $data
	 */
	public function setFromArray(array $data)
	{
		if (array_key_exists('categories', $data)) {
			$this->_categories = $data['categories'];
		}
		
		$date = new Zend_Date();
		$now = $date->toString('YYYY-MM-dd HH:mm:ss');

		if (null === $this->_data['created_at']) {
			$data['created_at'] = $now;
		}

		if (null === $this->_data['published_at']) {
			$data['published_at'] = $now;
		}
		
		$data['updated_at'] = $now;;

		if (null === $this->_data['user_id']) {
			$usersModel = new Publicada_Model_Users();
			$user = $usersModel->getByName(Zend_Auth::getInstance()->getIdentity());
			$data['user_id'] = $user->id;
		}

		parent::setFromArray($data);
	}
	
	/**
	 * 
	 * @return 
	 */
	public function toArray()
	{
		$data = parent::toArray();
		$data['categories'] = array();
		
		if (count($this->_categories)) {
			foreach ($this->_categories as $category) {
				$data['categories'][] = $category['id'];
			}
		}
		
		return $data;
	}
	
	/**
	 * 
	 * @return 
	 */
	public function init()
	{
		$p2pcs = $this->findManyToManyRowset('Publicada_Model_PostCategories',
											 'Publicada_Model_Posts2PostCategories');
		$this->_categories = $p2pcs->toArray();
	}
	
	/**
	 * 
	 * @param object $postCategoryIds
	 */
	protected function _updateCategories()
	{
		$p2pcModel = new Publicada_Model_Posts2PostCategories();
		$p2pcSelect = $p2pcModel->select()
								->where('post_id = ?', $this->id);
		$p2pcs = $p2pcModel->fetchAll($p2pcSelect);
		
		foreach ($p2pcs as $p2pc) {
			$p2pc->delete();
		}
		
		if (count($this->_categories)) {
			foreach ($this->_categories as $postCategoryId) {
				$p2pc = $p2pcModel->createRow();
				$p2pc->post_category_id = $postCategoryId;
				$p2pc->post_id = $this->id;
				$p2pc->save();
			}
		}
	}
	
	protected function _postInsert()
	{
		$this->_updateCategories();

		// add to index
		$index = $this->getTable()->getIndex();
		$index->addDocument(new Publicada_Search_IndexedPost($this));
		$index->commit();
		
		$this->clearCache();
	}
	
	protected function _postUpdate()
	{
		$this->_updateCategories();

		// replace in index
		$index = $this->getTable()->getIndex();
		
		$term = new Zend_Search_Lucene_Index_Term($this->id, 'post_id');

		foreach ($index->termDocs($term) as $match) {
        	$index->delete($match);
			$index->commit();
		}
		
		$index->addDocument(new Publicada_Search_IndexedPost($this));
		$index->commit();
		
		$this->clearCache();
		
	}
	
	protected function _postDelete()
	{
		$p2pcModel = new Publicada_Model_Posts2PostCategories();
		$p2pcSelect = $p2pcModel->select()
								->where('post_id = ?', $this->id);
		$p2pcs = $p2pcModel->fetchAll($p2pcSelect);
		
		foreach ($p2pcs as $p2pc) {
			$p2pc->delete();
		}

		// remove from index
		$index = $this->getTable()->getIndex();
		$term = new Zend_Search_Lucene_Index_Term($this->id, 'post_id');
		foreach ($index->termDocs($term) as $match) {
        	$index->delete($match);
		}
		$index->commit();
		
		$this->clearCache();
	}

	public function getImages()
	{
		$imageSelect = $this->select()->where('type = ?', self::FILE_TYPE_IMAGE);
		$images = $this->findManyToManyRowset('Publicada_Model_Files',
											 'Publicada_Model_Posts2Files',
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
			array('posts')
		);
	}
	
	public function getUser() {
		$usersModel = Publicada_Model_Users::getInstance();
		$user = $usersModel->getById($this->user_id);
		
		return $user;
	}
}
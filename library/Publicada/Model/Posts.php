<?php

class Publicada_Model_Posts extends Zend_Db_Table_Abstract
{
	const STATUS_DRAFT 			= 0;
	const STATUS_PROPOSED 		= 1;
	const STATUS_PUBLISHED 		= 2;
	const DEFAULT_PAGE_SIZE		= 16;
	const DEFAULT_PAGE_AT		= 1;

    /**
     * Singleton instance
     *
     * Marked only as protected to allow extension of the class. To extend,
     * simply override {@link getInstance()}.
     *
     * @var Publicada_Model_Posts
     */
    protected static $_instance = null;

	protected $_name			= 'posts';
	protected $_primary			= 'id';
	protected $_rowClass		= 'Publicada_Model_Post';
	protected $_dependentTables = array('Publicada_Model_Posts2PostCategories', 'Publicada_Model_Posts2Files');
	
	protected $_referenceMap = array(
		'users' => array(
			'columns'		=> array('user_id'),
			'refTableClass'	=> 'Publicada_Model_Users',
			'refColumns'	=> array('id')
		)
	);
	
    /**
     * Singleton instance
     *
     * @return Publicada_Model_Posts
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

	/**
	 * Get a post based on it's id
	 * 
	 * @return	Publicada_Model_Post	$postRow	The resulting post row 
	 * 
	 * @param	integer					$postId		The id of the post
	 */
	public function getById($postId)
	{
		$postSelect = $this->select()
						   ->where('id = ?', $postId, 'integer');
		$postRow = $this->fetchRow($postSelect);
		return $postRow;
	}
	
	/**
	 * 
	 * @return Zend_Db_Table_Rowset
	 * 
	 * @param object $limit[optional]
	 * @param object $page[optional]
	 * @param object $options[optional]
	 */
	public function getPosts($limit = 16, $page = 1, $options = array())
	{
		$postSelect = $this->select();
		$postSelect->from(array('p' => 'posts'))
				   ->where('language = ?', Zend_Registry::get('content_locale'))
				   ->order('published_at DESC');

		if (!array_key_exists('status', $options)) {
			$postSelect->where('p.status = ?', Publicada_Model_Posts::STATUS_PUBLISHED);
		}
		else {
			if ($options['status'] == Publicada_Model_Posts::STATUS_DRAFT) {
				$postSelect->where('p.status = ?', Publicada_Model_Posts::STATUS_DRAFT);
			}
			elseif ($options['status'] == Publicada_Model_Posts::STATUS_PROPOSED) {
				$postSelect->where('p.status = ?', Publicada_Model_Posts::STATUS_PROPOSED);
			}
		}
		$paginator = Zend_Paginator::factory($postSelect);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($limit);
		return $paginator;
	}
	
	public function getAll($options = array())
	{
		$postsSelect = $this->select()->order('updated_at DESC');
		if (array_key_exists('order', $options)) {
			$postsSelect->order($options['order']);
		}
		if (array_key_exists('lang', $options)) {
			$postsSelect->where('language = ?', $options['lang']);
		}
		if (array_key_exists('status', $options)) {
			$postsSelect->where('status = ?', $options['status']);
		}
		if (array_key_exists('paginate', $options)) {
			if (true === $options['paginate']) {
				$posts = Zend_Paginator::factory($postsSelect);
				if (array_key_exists('page_at', $options)) {
					$posts->setCurrentPageNumber($options['page_at']);
				}
				else {
					$posts->setCurrentPageNumber(self::DEFAULT_PAGE_AT);
				}
				if (array_key_exists('page_size', $options)) {
					$posts->setItemCountPerPage($options['page_size']);
				}
				else {
					$posts->setItemCountPerPage(self::DEFAULT_PAGE_SIZE);
				}
			}
			else {
				$posts = $this->fetchAll($postsSelect);
			}
		}
		else {
			$posts = $this->fetchAll($postsSelect);
		}
		return $posts;
	}
	
	public function reIndex()
	{
		set_time_limit(1800);
		$index = $this->_getIndex();
		$term = new Zend_Search_Lucene_Index_Term('post', 'content_type');

		foreach ($index->termDocs($term) as $match) {
        	$index->delete($match);
			$index->commit();
		}
		$posts = $this->getAll();
		foreach ($posts as $post) {
			$index->addDocument(new Publicada_Search_IndexedPost($post));
			$index->commit();
		}
		
		$index->optimize();

	}
	
	protected function _getIndex()
	{
		Zend_Search_Lucene_Analysis_Analyzer::setDefault(
			new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8_CaseInsensitive ());

		$configuration = Zend_Registry::get('configuration');
		$indexPath = $configuration['index_path'];
		try {
			$index =  Zend_Search_Lucene::open($indexPath);
		}
		catch (Exception $e) {
			$index =  Zend_Search_Lucene::create($indexPath);
		}
		return $index;
	}
	
	public function getIndex() {
		return $this->_getIndex();
	}
}
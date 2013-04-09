<?php

class Publicada_Model_Sources extends Zend_Db_Table_Abstract
{
	const STATUS_DRAFT 			= 0;
	const STATUS_PROPOSED 		= 1;
	const STATUS_PUBLISHED 		= 2;
	const DEFAULT_PAGE_SIZE		= 16;
	const DEFAULT_PAGE_AT		= 1;
	

    protected static $_instance = null;

	protected $_name			= 'sources';
	protected $_primary			= 'id';
	protected $_rowClass		= 'Publicada_Model_Source';
	
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

	public function getById($id)
	{
		$select = $this->select()->where('id = ?', $id, 'integer');
		$row = $this->fetchRow($select);
		return $row;
	}
	
	public function getAll($options = array())
	{
		$adsSelect = $this->select()->order('updated_at DESC');
		if (array_key_exists('order', $options)) {
			$adsSelect->order($options['order']);
		}
		if (array_key_exists('lang', $options)) {
			$adsSelect->where('language = ?', $options['lang']);
		}
//		if (array_key_exists('status', $options)) {
//			$adsSelect->where('status = ?', $options['status']);
//		}
		if (array_key_exists('paginate', $options)) {
			if (true === $options['paginate']) {
				$ads = Zend_Paginator::factory($adsSelect);
				if (array_key_exists('page_at', $options)) {
					$ads->setCurrentPageNumber($options['page_at']);
				}
				else {
					$ads->setCurrentPageNumber(self::DEFAULT_PAGE_AT);
				}
				if (array_key_exists('page_size', $options)) {
					$ads->setItemCountPerPage($options['page_size']);
				}
				else {
					$ads->setItemCountPerPage(self::DEFAULT_PAGE_SIZE);
				}
			}
			else {
				$ads = $this->fetchAll($postsSelect);
			}
		}
		else {
			$ads = $this->fetchAll($adsSelect);
		}
		return $ads;
	}
	
//	public function reIndex()
//	{
//		$index = $this->_getIndex();
//		$term = new Zend_Search_Lucene_Index_Term('post', 'content_type');
//		foreach ($index->termDocs($term) as $match) {
//        	$index->delete($match);
//			$index->commit();
//		}
//		$posts = $this->getAll();
//		foreach ($posts as $post) {
//			$index->addDocument(new Publicada_Search_IndexedPost($post));
//			$index->commit();
//		}
//
//		$index->optimize();
//	}
	
//	protected function _getIndex()
//	{
//		set_time_limit(1800);
//		$configuration = Zend_Registry::get('configuration');
//		$indexPath = $configuration['ad_index_path'];
//		try {
//			$index =  Zend_Search_Lucene::open($indexPath);
//		}
//		catch (Exception $e) {
//			$index =  Zend_Search_Lucene::create($indexPath);	
//		}
//		return $index;
//	}
	
//	public function getIndex() {
//		return $this->_getIndex();
//	}
}
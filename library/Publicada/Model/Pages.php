<?php

class Publicada_Model_Pages extends Zend_Db_Table_Abstract
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
     * @var Publicada_Model_Pages
     */
    protected static $_instance = null;
	
	protected $_name			= 'pages';
	protected $_primary			= 'id';
	protected $_rowClass		= 'Publicada_Model_Page';
	protected $_dependentTables = array('Publicada_Model_Pages', 'Publicada_Model_Posts2Files');
	
	protected $_referenceMap = array(
		'children' => array(
			'columns'		=> array('parent_code'),
			'refTableClass'	=> 'Publicada_Model_Pages',
			'refColumns'	=> array('code')
		)
	);
	
	
    /**
     * Singleton instance
     *
     * @return Publicada_Model_Pages
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

	/**
	 * Get published page row based on it's code
	 *
	 * @param	string					$pageCode	Code to search for
	 * @return	Publicada_Model_Page	$page		Resulting page row
	 */
	public function getByCode($pageCode)
	{
		$pageSelect = $this->select()
						   ->where('code = ?', $pageCode)
						   ->where('language = ?', Zend_Registry::get('content_locale'))
						   ->where('status = ?', Publicada_Model_Pages::STATUS_PUBLISHED);
		$pageRow = $this->fetchRow($pageSelect);
		
		return $pageRow;
	}

	/**
	 * Get page row
	 *
	 * @param	integer					$pageId		Id of the desired page
	 * @return	Publicada_Model_Page	$page		Resulting page row
	 */
	public function getById($pageId)
	{
		return $this->fetchRow($this->select()->where('id = ?', $pageId));
	}
	
	public function getAll($options = array())
	{
		$pagesSelect = $this->select()->order('updated_at DESC');

		if (array_key_exists('order', $options)) {
			$pagesSelect->order($options['order']);
		}
		
		if (array_key_exists('lang', $options)) {
			$pagesSelect->where('language = ?', $options['lang']);
		}
		
		if (array_key_exists('status', $options)) {
			$pagesSelect->where('status = ?', $options['status']);
		}

		if (array_key_exists('paginate', $options)) {
			if (true === $options['paginate']) {
				$pages = Zend_Paginator::factory($pagesSelect);
				
				if (array_key_exists('page_at', $options)) {
					$pages->setCurrentPageNumber($options['page_at']);
				}
				else {
					$pages->setCurrentPageNumber(self::DEFAULT_PAGE_AT);
				}

				if (array_key_exists('page_size', $options)) {
					$pages->setItemCountPerPage($options['page_size']);
				}
				else {
					$pages->setItemCountPerPage(self::DEFAULT_PAGE_SIZE);
				}
			}
			else {
				$pages = $this->fetchAll($pagesSelect);
			}
		}
		else {
			$pages = $this->fetchAll($pagesSelect);
		}
		return $pages;
	}

	public function reIndex()
	{
		set_time_limit(1800);
		$index = $this->_getIndex();
		
		$term = new Zend_Search_Lucene_Index_Term('page', 'content_type');

		foreach ($index->termDocs($term) as $match) {
        	$index->delete($match);
			$index->commit();
		}
		
		
		$pages = $this->getAll();

		foreach ($pages as $page) {
			$index->addDocument(new Publicada_Search_IndexedPage($page));
			$index->commit();
		}

		$index->optimize();

	}
	
	protected function _getIndex()
	{
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
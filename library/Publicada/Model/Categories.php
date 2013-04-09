<?php

class Publicada_Model_Categories extends Zend_Db_Table_Abstract
{
	const STATUS_DRAFT 			= 0;
	const STATUS_PROPOSED 		= 1;
	const STATUS_PUBLISHED 		= 2;
	const DEFAULT_PAGE_SIZE		= 16;
	const DEFAULT_PAGE_AT		= 1;
	protected $_itemsName		= null;
	protected $_primary			= 'id';
	protected $_connectionsName	= null;

    /**
     * Singleton instance
     *
     * Marked only as protected to allow extension of the class. To extend,
     * simply override {@link getInstance()}.
     *
     * @var Publicada_Model_PostCategories
     */
    protected static $_instance = null;
	
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
	 * Get post category row
	 *
	 * @param	string				$code			Code to search for
	 * @return	Zend_Db_Table_Row	$postCategory	Resulting post category row
	 */
	public function getByCode($code)
	{
		$postCategorySelect = $this->select()
									->where('code = ?', $code)
									->where('language = ?', Zend_Registry::get('content_locale'));
		/**
		 * TODO: implement language
		 */
		$postCategory = $this->fetchRow($postCategorySelect);
		return $postCategory;
	}

	/**
	 * Get post category row
	 *
	 * @param	integer					$postCategoryId		Code to search for
	 * @return	Zend_Db_Table_Rowset	$postCategory		Resulting post category row
	 */
	public function getById($postCategoryId)
	{
		return $this->fetchRow($this->select()->where('id = ?', $postCategoryId));
	}

	public function getCategories($parentCode, $options = array())
	{
		$categorySelect = $this->select();
		$categorySelect->where('parent_code = ?', $parentCode)
						->where('language = ?', Zend_Registry::get('content_locale'))
						->order('name ASC');
		$categories = $this->fetchAll($categorySelect);

		return $categories;
	}
	
	/**
	 * TODO: add documentation
	 * @return 
	 */

	public function getAll($options = array())
	{
		$categoriesSelect = $this->select();

		$categoriesSelect->where('language = ?', Zend_Registry::get('content_locale'));
		
		if (array_key_exists('parent_code', $options)) {
			$categoriesSelect->where('parent_code = ?', $options['parent_code']);
		}
		
		/**
		 * TODO: add language support
		 */

		if (array_key_exists('paginate', $options)) {
			if ($options['paginate'] == true) {
				$categories = Zend_Paginator::factory($categoriesSelect);
				
			}
			if (array_key_exists('limit', $options)) {
				$categories->setItemCountPerPage($options['limit']);
			}
			if (array_key_exists('page', $options)) {
				$categories->setCurrentPageNumber($options['page']);
			}
		}

		else {
			$categories = $this->fetchAll($categoriesSelect);
		}
		
		return $categories;
	}

	public function getItemsName() {
		return $this->_itemsName;
	}

	public function getConnectionsName() {
		return $this->_connectionsName;
	}
}
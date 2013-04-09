<?php

class Publicada_Model_Options extends Zend_Db_Table_Abstract
{
	protected $_name			= 'options';
	protected $_primary			= 'id';
	protected $_rowClass		= 'Publicada_Model_Option';
	

    /**
     * Singleton instance
     *
     * Marked only as protected to allow extension of the class. To extend,
     * simply override {@link getInstance()}.
     *
     * @var Publicada_Model_Options
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
	 * Get option by code and content_locale
	 * 
	 * @param	string					$code	The code of the desired option
	 * @return	Publicada_Model_Option	$option	Resuling option row
	 */
	public function getByCode($optionCode)
	{
		$optionSelect = $this->select()
					   ->where('option_code = ?', $optionCode)
					   ->where('language = ?', Zend_Registry::get('content_locale'));
		$optionRow = $this->fetchRow($optionSelect);
		
		return $optionRow;
	}
	
	/**
	 * Get option by id
	 * 
	 * @return Publicada_Model_Option
	 * @param integer $optionId
	 */
	public function getById($optionId)
	{
		$optionSelect = $this->select()
					   ->where('id = ?', $optionId);
		$optionRow = $this->fetchRow($optionSelect);
		
		return $optionRow;
	}
	
	/**
	 * 
	 * @return Zend_Paginator $optionsPaginator
	 * @param object $limit[optional]
	 * @param object $page[optional]
	 * @param object $options[optional]
	 */
	public function getOptions($limit = 16, $page = 1, $options = array())
	{
		$optionsSelect = $this->select()->where('language = ?', Zend_Registry::get('content_locale'));
		$optionsPaginator = Zend_Paginator::factory($optionsSelect);
		$optionsPaginator->setCurrentPageNumber($page);
		$optionsPaginator->setItemCountPerPage($limit);
		
		return $optionsPaginator;
	}
	
}
<?php

class Publicada_Model_Users extends Zend_Db_Table_Abstract
{
	protected $_name			= 'users';
	protected $_primary			= 'id';
	protected $_rowClass		= 'Publicada_Model_User';
	protected $_dependentTables = array('Publicada_Model_Posts', 'Publicada_Model_Comments');
	

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

	public function getByName($userName)
	{
		$userSelect = $this->select()->where("username = ?", $userName);
		$user = $this->fetchRow($userSelect);

		return $user;
	}

	public function getById($id)
	{
		$userSelect = $this->select()->where("id = ?", $id);
		$user = $this->fetchRow($userSelect);

		return $user;
	}
}
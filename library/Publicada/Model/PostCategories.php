<?php

class Publicada_Model_PostCategories extends Publicada_Model_Categories
{
	protected $_name			= 'categories';
	protected $_primary			= 'id';
	protected $_rowClass		= 'Publicada_Model_PostCategory';
	protected $_dependentTables = array('Publicada_Model_Posts2PostCategories');
	protected $_itemsName		= 'posts';
	protected $_connectionsName	= 'posts2post_categories';

    protected static $_instance = null;
	
	public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}
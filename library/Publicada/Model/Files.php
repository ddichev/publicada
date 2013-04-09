<?php

class Publicada_Model_Files extends Zend_Db_Table_Abstract
{
	const		FILE_TYPE_BASIC = 0;

	protected	$_name = 'files';
	protected	$_primary = 'id';
	protected	$_dependentTables = array('Publicada_Model_Posts2Files', 'Publicada_Model_Pages2Files');

    protected static $_instance = null;
	
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

	public function getAll($limit = 16, $page = 1, $options = array())
	{
		$filesSelect = $this->select();
		
		if (array_key_exists('type', $options)) {
			$filesSelect->where('type = ?', $options['type'], 'integer');
		}

		$filesPaginator = Zend_Paginator::factory($filesSelect);
		$filesPaginator->setCurrentPageNumber($page);
		$filesPaginator->setItemCountPerPage($limit);
		
		return $filesPaginator;
	}
	
	public function getById($fileId)
	{
		$fileSelect = $this->select()->where('id = ?', $fileId);
		$file = $this->fetchRow($fileSelect);
		
		return $file;
	}
}
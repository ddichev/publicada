<?php

class Publicada_Model_Pages2Files extends Zend_Db_Table_Abstract
{
	protected $_name			= 'pages2files';
	protected $_primary			= array('page_id', 'file_id');
	
	protected $_referenceMap = array(
		'pages' => array(
			'columns'		=> array('page_id'),
			'refTableClass'	=> 'Publicada_Model_Pages',
			'refColumns'	=> array('id'),
        	'onDelete'		=>  self::CASCADE
		),
		'files' => array(
			'columns'		=> array('file_id'),
			'refTableClass'	=> 'Publicada_Model_Files',
			'refColumns'	=> array('id'),
        	'onDelete'		=>  self::CASCADE
		)
	);
}
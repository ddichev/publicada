<?php

class Publicada_Model_Posts2Files extends Zend_Db_Table_Abstract
{
	protected $_name			= 'posts2files';
	protected $_primary			= array('post_id', 'file_id');
	
	protected $_referenceMap = array(
		'pages' => array(
			'columns'		=> array('post_id'),
			'refTableClass'	=> 'Publicada_Model_Posts',
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
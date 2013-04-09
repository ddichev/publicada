<?php

class Publicada_Model_Pages2Comments extends Zend_Db_Table_Abstract
{
	protected $_name			= 'pages2comments';
	protected $_primary			= array('page_id', 'comment_id');
	
	protected $_referenceMap = array(
		'pages' => array(
			'columns'		=> array('page_id'),
			'refTableClass'	=> 'Publicada_Model_Pages',
			'refColumns'	=> array('id'),
        	'onDelete'		=>  self::CASCADE
		),
		'comments' => array(
			'columns'		=> array('comment_id'),
			'refTableClass'	=> 'Publicada_Model_Comments',
			'refColumns'	=> array('id'),
        	'onDelete'		=>  self::CASCADE
		)
	);
}
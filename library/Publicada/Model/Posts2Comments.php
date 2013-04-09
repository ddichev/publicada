<?php

class Publicada_Model_Posts2Comments extends Zend_Db_Table_Abstract
{
	protected $_name			= 'posts2comments';
	protected $_primary			= array('post_id', 'comment_id');
	
	protected $_referenceMap = array(
		'posts' => array(
			'columns'		=> array('post_id'),
			'refTableClass'	=> 'Publicada_Model_Posts',
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
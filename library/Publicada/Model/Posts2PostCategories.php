<?php

class Publicada_Model_Posts2PostCategories extends Zend_Db_Table_Abstract
{
	protected $_name			= 'posts2post_categories';
	protected $_primary			= 'id';
	
	protected $_referenceMap = array(
		'posts' => array(
			'columns'		=> array('post_id'),
			'refTableClass'	=> 'Publicada_Model_Posts',
			'refColumns'	=> array('id')
		),
		'categories' => array(
			'columns'		=> array('post_category_id'),
			'refTableClass'	=> 'Publicada_Model_PostCategories',
			'refColumns'	=> array('id')
		)
	);
}
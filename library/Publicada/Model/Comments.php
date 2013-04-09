<?php

class Publicada_Model_Comments extends Zend_Db_Table_Abstract
{
	const STATUS_DRAFT 			= 0;
	const STATUS_PROPOSED 		= 1;
	const STATUS_PUBLISHED 		= 2;
	const DEFAULT_PAGE_SIZE		= 16;
	const DEFAULT_PAGE_AT		= 1;

    /**
     * Singleton instance
     *
     * Marked only as protected to allow extension of the class. To extend,
     * simply override {@link getInstance()}.
     *
     * @var Publicada_Model_Posts
     */
    protected static $_instance = null;

	protected $_name			= 'comments';
	protected $_primary			= 'id';
	protected $_rowClass		= 'Publicada_Model_Comment';
	protected $_dependentTables = array('Publicada_Model_Posts2Comments', 'Publicada_Model_Pages2Comments');
	
	protected $_referenceMap = array(
		'users' => array(
			'columns'		=> array('user_id'),
			'refTableClass'	=> 'Publicada_Model_Users',
			'refColumns'	=> array('id')
		)
	);
	
    /**
     * Singleton instance
     *
     * @return Publicada_Model_Posts
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

	/**
	 * Get a post based on it's id
	 * 
	 * @return	Publicada_Model_Post	$postRow	The resulting post row 
	 * 
	 * @param	integer					$postId		The id of the post
	 */
	public function getById($id)
	{
		$select = $this->select()
						   ->where('id = ?', $id, 'integer');
		$commment = $this->fetchRow($select);
		return $commment;
	}
	
	/**
	 * 
	 * @return Zend_Db_Table_Rowset
	 * 
	 * @param object $limit[optional]
	 * @param object $page[optional]
	 * @param object $options[optional]
	 */
	public function getPosts($limit = 16, $page = 1, $options = array())
	{
		$select = $this->select();
		$select->from(array('c' => 'comments'))
				   ->where('language = ?', Zend_Registry::get('content_locale'))
				   ->order('published_at DESC');

		if (!array_key_exists('status', $options)) {
			$select->where('c.status = ?', Publicada_Model_Comments::STATUS_PUBLISHED);
		}
		else {
			if ($options['status'] == Publicada_Model_Comments::STATUS_DRAFT) {
				$select->where('c.status = ?', Publicada_Model_Comments::STATUS_DRAFT);
			}
			elseif ($options['status'] == Publicada_Model_Comments::STATUS_PROPOSED) {
				$select->where('c.status = ?', Publicada_Model_Comments::STATUS_PROPOSED);
			}
		}
		$paginator = Zend_Paginator::factory($select);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($limit);
		return $paginator;
	}
	
	public function getAll($options = array())
	{
		$select = $this->select()->order('updated_at DESC');
		if (array_key_exists('order', $options)) {
			$select->order($options['order']);
		}
		if (array_key_exists('lang', $options)) {
			$select->where('language = ?', $options['lang']);
		}
		if (array_key_exists('status', $options)) {
			$select->where('status = ?', $options['status']);
		}
		if (array_key_exists('paginate', $options)) {
			if (true === $options['paginate']) {
				$comments = Zend_Paginator::factory($select);
				if (array_key_exists('page_at', $options)) {
					$comments->setCurrentPageNumber($options['page_at']);
				}
				else {
					$comments->setCurrentPageNumber(self::DEFAULT_PAGE_AT);
				}
				if (array_key_exists('page_size', $options)) {
					$comments->setItemCountPerPage($options['page_size']);
				}
				else {
					$comments->setItemCountPerPage(self::DEFAULT_PAGE_SIZE);
				}
			}
			else {
				$comments = $this->fetchAll($select);
			}
		}
		else {
			$comments = $this->fetchAll($select);
		}
		return $comments;
	}
}
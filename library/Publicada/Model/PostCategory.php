<?php

class Publicada_Model_PostCategory extends Publicada_Model_Category
{

	protected function _getItemsSelect($options = array())
	{
		$subcategoryIds = $this->_extractSubcategoryIds();
		
		$itemsSelect = $this->getTable()->getAdapter()->select();
		$itemsSelect->from(array('items' => $this->getTable()->getItemsName()))
					->where('items.language = ?', $this->language)
					->join(
						array('i2c' => $this->getTable()->getConnectionsName()),
						'i2c.post_id = items.id',
						array('id' => 'items.id'))
					->order('published_at DESC')
					->group('items.id');

		$dbAdapter = $this->getTable()->getAdapter();
		$multipleCategoriesConditions = array();
		
		foreach ($subcategoryIds as $subcategoryId) {
			$multipleCategoriesConditions[] = $dbAdapter->quoteInto('i2c.post_category_id = ?',
																	$subcategoryId);
		}
		$itemsSelect->where(implode(' OR ', $multipleCategoriesConditions));		

		if (!array_key_exists('status', $options)) {
			$itemsSelect->where('items.status = ?', Publicada_Model_Posts::STATUS_PUBLISHED);
		}
		else {
			if ($options['status'] == Publicada_Model_Posts::STATUS_DRAFT) {
				$itemsSelect->where('items.status = ?', Publicada_Model_Posts::STATUS_DRAFT);
			}
			elseif ($options['status'] == Publicada_Model_Posts::STATUS_PROPOSED) {
				$itemsSelect->where('items.status = ?', Publicada_Model_Posts::STATUS_PROPOSED);
			}
		}

		return $itemsSelect;
	}

	/**
	 * @param	integer	$limit
	 * @param	integer	$page
	 * @param	array	$options
	 * 
	 * @return	Zend_Paginator	$paginator
	 */
	public function getPosts($limit = 16, $page = 1, $options = array())
	{
		$posts = parent::getItems($limit, $page, $options);
		$postsModel = Publicada_Model_Posts::getInstance();
		foreach ($posts as &$post) {
			$postObject = $postsModel->createRow();
			$postArray = (array) $post;
			$postObject->setFromArray( $postArray );
			$post = $postObject;
		}
		return $posts;
	}
	
	public function getPost()
	{
		$post = $this->getTable()->getAdapter()->fetchRow($this->_getItemsSelect());
		return $post;
	}
}
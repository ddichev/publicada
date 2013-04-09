<?php

class Publicada_Model_Category extends Zend_Db_Table_Row_Abstract
{
	protected	$_allCategories = null;
	
	protected	$_filterCategories = array();

	public		$subcategoriesTotalCount = null;
	
	public function addFilterCategory(Publicada_Model_Category $category) {
		$filterCategoriesCount = count($this->_filterCategories);
		$alreadyInFilterCategories = false;

		if (count($filterCategoriesCount) > 0) {
			for ($i = 0; $i < $filterCategoriesCount; $i++) {
				if ($this->_filterCategories[$i]->id == $category->id) {
					$alreadyInFilterCategories = true;
				}
			}
		}
		
		if (false == $alreadyInFilterCategories) {
			$this->_filterCategories[] = $category;
		}
	}
	
	public function removeFilterCategory(Publicada_Model_Category $category) {
		$filterCategoriesCount = count($this->_filterCategories);
		if ($filterCategoriesCount) {
			for ($i = 0; $i < $filterCategoriesCount; $i++) {
				if ($this->_filterCategories[$i]->id == $category->id) {
					array_splice($this->_filterCategories, $i);
				}
			}
		}
	}
	
	protected function _extractSubcategoryIds($category = null, $subcategoryIds = null)
	{
		if ($subcategoryIds == null) {
			$subcategoryIdsInner = array();
			$subcategoryIdsInner[] = $this->id;
		}
		else {
			$subcategoryIdsInner = $subcategoryIds;
		}
		
		if ($category != null) {
			$subcategories = $category->getSubcategories();
		}
		else {
			$subcategories = $this->getSubcategories();
		}
		
			foreach ($subcategories as $subcategory) {
				$subcategoryIdsInner[] = $subcategory->id;
//				Zend_Debug::dump($subcategoryIds);
//				Zend_Debug::dump($this->_extractSubcategoryIds($subcategory, $subcategoryIds));
				$subcategoryIdsInner = array_merge($subcategoryIdsInner, $subcategory->_extractSubcategoryIds());
				$subcategoryIdsInner = array_unique($subcategoryIdsInner);
			}
//			Zend_Debug::dump($subcategoryIdsInner); die();
		
		return $subcategoryIdsInner;
	}

	public function getSubcategories($options = array())
	{
		$order = 'name ASC';

//		try {
//			$allCategories = Zend_Registry::get('all_categories');			
//		}
//		catch (Exception $e) {
//			$allCategories = $this->getTable()->fetchAll($this->select()->order($order));
//			Zend_Registry::set('all_categories', $allCategories);
//		}
//		
//		$subcategories = array();
//		
//		foreach ($allCategories as $category) {
//			if ($category->parent_code == $this->code) {
//				$subcategories[] = $category;
//			}
//		}
	
		$select = $this->select()->where('parent_code = ?', $this->code);
		$select->where('language = ?', $this->language);
		if (array_key_exists('status', $options)) {
			$select->where('status = ?', $options['status']);
		}
		elseif (array_key_exists('getall', $options)) {
			if (true === $options['getall']) {
				// do nothing
			}
		}
		else {
			$select->where('status = ?', Publicada_Model_Categories::STATUS_PUBLISHED);
		}
		if (array_key_exists('order', $options)) {
			$select->order($options['order']);
		}
		else {
			$select->order($order);
		}
		
		if (array_key_exists('limit', $options)) {
			$select->limit(0, $options['limit']);
		}
		$subcategories = $this->getTable()->fetchAll($select);
		
		return $subcategories;
	}
	
	public function getItems($limit = 16, $page = 1, $options = array())
	{
		$itemsSelect = $this->_getItemsSelect($options);
		$paginator = Zend_Paginator::factory($itemsSelect);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($limit);
		return $paginator;
	}
	
	public function setFromArray(array $data)
	{
		if ($data['order'] == "") {
			$data['order'] = 0;
		}
		
		$date = new Zend_Date();
		$now = $date->toString('YYYY-MM-dd HH:mm:ss');

		if (null === $this->_data['created_at']) {
			$data['created_at'] = $now;
		}

		if (null === $this->_data['published_at']) {
			$data['published_at'] = $now;
		}
		
		$data['updated_at'] = $now;

		if (empty($data['user_id'])) {
			$data['user_id'] = Zend_Auth::getInstance()->getIdentity()->id;
		}
		
		parent::setFromArray($data);
	}
	
	protected function _postInsert()
	{
		$this->clearCache();
	}
	
	protected function _postUpdate ()
	{
		if ($this->code != $this->_cleanData['code']) {
			$subcategories = $this->getTable()->fetchAll($this->select()->where('parent_code = ?', $this->_cleanData['code']));
			foreach ($subcategories as $subcategory) {
				$subcategory->parent_code = $this->code;
				$subcategory->save();
			}
		}
		
		$this->clearCache();
	}
	
	protected function _postDelete ()
	{
		$subcategories = $this->getTable()->fetchAll($this->select()->where('parent_code = ?', $this->_cleanData['code']));
		foreach ($subcategories as $subcategory) {
			$subcategory->parent_code = $this->parent_code;
			$subcategory->save();
		}
		
		$this->clearCache();
	}
	
	public function getParentCategory()
	{
		return $this->getTable()->fetchRow($this->select()->where('code = ?', $this->parent_code));
	}
	
	public function clearCache() {
		$cacheConfig = new Zend_Config(require APPLICATION_PATH . '/configs/cache.php');
		$frontendOptions = $cacheConfig->frontendOptions->toArray();
		$backendOptions  = $cacheConfig->backendOptions->toArray();

        $cache = Zend_Cache::factory('Page',
                                     'File',
                                     $frontendOptions,
                                     $backendOptions);
        $cache->clean(
			Zend_Cache::CLEANING_MODE_MATCHING_TAG,
			array('categories')
		);
	}
}
<?php

class Publicada_Model_Option extends Zend_Db_Table_Row_Abstract
{
	/**
	 * Get the value of an option based on the option's name.
	 *
	 * @return	string	$optionValue	The options's value.
	 */
	public function getValue()
	{
		return $this->option_value;
	}
	
	protected function _postInsert()
	{
		$this->clearCache();
	}
	
	protected function _postUpdate()
	{
		$this->clearCache();
	}
	
	protected function _postDelete()
	{
		$this->clearCache();
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
			array('options')
		);
	}
}
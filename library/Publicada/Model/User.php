<?php

class Publicada_Model_User extends Zend_Db_Table_Row_Abstract
{
	public static function doesAuthenticate($userName, $passWord)
	{
		
		$authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Registry::get('db'));
		$authAdapter->setTableName('users');
		$authAdapter->setIdentityColumn('username');
		$authAdapter->setCredentialColumn('password');
		$authAdapter->setIdentity($userName);
		$authAdapter->setCredential(md5($passWord));
		$result = Zend_Auth::getInstance()->authenticate($authAdapter);

		return $result->isValid();
	}

	
	public function changePassword($newPassword)
	{
		$this->password = md5($newPassword);
		$this->save();
	}
	
	public function setFromArray(array $data)
	{
		if (empty($data['nick'])) {
			$data['nick'] = $data['username'];
		}
		parent::setFromArray($data);
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
			array('users')
		);
	}
}
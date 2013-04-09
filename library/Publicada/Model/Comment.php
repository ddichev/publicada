<?php

class Publicada_Model_Comment extends Zend_Db_Table_Row_Abstract
{
	/**
	 * 
	 * @return 
	 * @param object $data
	 */
	public function setFromArray(array $data)
	{
		$date = new Zend_Date();
		$now = $date->toString('YYYY-MM-dd HH:mm:ss');

		if (null === $this->_data['created_at']) {
			$data['created_at'] = $now;
		}

		$this->updated_at = $now;

		if (array_key_exists('user_id', $this->_data)) {
			if (Zend_Auth::getInstance()->hasIdentity()) {
				$usersModel = new Publicada_Model_Users();
				$user = $usersModel->getByName(Zend_Auth::getInstance()->getIdentity());
				$data['user_id'] = $user->id;
			}
		}

		parent::setFromArray($data);
	}
	
	public function hasUser()
	{
		$userId = (int) $this->user_id;
		if ($userId > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public function getUser()
	{
		$userId = (int) $this->user_id;
		if ($userId > 0) {
			$usersModel = Publicada_Model_Users::getInstance();
			return $usersModel->getById($userId);
		}
		
		return null;
	}
}
<?php

class Publicada_View_Helper_GetIdentity
{
	public function getIdentity() {
		$auth = Zend_Auth::getInstance();
		
		if ($auth->hasIdentity()) {
			return $auth->getIdentity();
		}
		else {
			return 'unidentified';
		}
	}
}
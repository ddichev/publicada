<?php

class Publicada_View_Helper_HasIdentity
{
	public function hasIdentity() {
		$auth = Zend_Auth::getInstance();
		return $auth->hasIdentity();
	}
}
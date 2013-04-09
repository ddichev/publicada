<?php

class Publicada_View_Helper_GetBody extends Zend_View_Helper_Abstract
{
	public function getBody($code = 'root')
	{
		$pagesModel = Publicada_Model_Pages::getInstance();
		$sidebarPage = $pagesModel->fetchRow($pagesModel->select()->where('code = ?', $code));
		return $sidebarPage ? $sidebarPage->body : null;
	}
}
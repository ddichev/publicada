<?php

class Publicada_View_Helper_IndexUrl extends Zend_View_Helper_Abstract
{
	public function indexUrl()
	{
		$langParam = Zend_Registry::get('content_locale');
		
		$configuration = Zend_Registry::get('configuration');
		$defaultSiteLang = $configuration['languages']['1'];
		
		if (null === $langParam || $langParam == $defaultSiteLang)
		{
			return Zend_Controller_Front::getInstance()->getBaseUrl() . '/';
		}
		else
		{
			return Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . Zend_Controller_Front::getInstance()->getRequest()->getParam('lang');
		}
	}
}
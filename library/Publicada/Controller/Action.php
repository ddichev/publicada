<?php

class Publicada_Controller_Action extends Zend_Controller_Action
{
	const DEFAULT_PAGE_SIZE = 16;
	const DEFAULT_TITLE_CONNECTOR = '-';
	
	public function getModuleName()
	{
		return Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
	}
	
	public function getControllerName()
	{
		return Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
	}

	public function getActionName()
	{
		return Zend_Controller_Front::getInstance()->getRequest()->getActionName();
	}
	
	public function getSiteRoot()
	{
		return APPLICATION_PATH;
	}
	
	public function getConfig()
	{
		return Zend_Registry::getInstance()->configuration;
	}
	
	
	protected function _getControllerFormPath()
	{
		$controllerFormPath = $this->getModuleDirectory() . '/forms/' . $this->getControllerName() . '.ini';
		
		return $controllerFormPath;
	}
	
	protected function _getActionUrl()
	{
		$actionUrl = $this->view->url(
						array(
							'controller' => $this->getControllerName(),
							'action' => $this->getActionName()
						));
		
		return $actionUrl;
	}
	
	protected function _getActionForm()
	{
		$config = new Zend_Config_Ini($this->_getControllerFormPath(), $this->getActionName());
		$form = new Zend_Form($config->{$this->getControllerName()}->{$this->getActionName()});
		$form->setAction($this->_getActionUrl());
		
		if ($form->getElement('language') != null) {
			$configuration = Zend_Registry::get('configuration');
			foreach ($configuration['languages'] as $langKey => $langValue) {
				$form->getElement('language')->addMultiOption($langValue, $langValue);
			}
		}
		
		return $form;
	}
	
	protected function _validCaptcha() {
		$captcha = $this->getRequest()->getPost('captcha');
		$captchaInput = $captcha['input'];
		$captchaSession = new Zend_Session_Namespace('Zend_Form_Captcha_'.$captcha['id']);
		$captchaIterator = $captchaSession->getIterator();  
		if ($captchaInput == $captchaIterator['word']) {
			return true;
		}
		else {
			return false;
		}
	}
	
	protected function _hasIdentity() {
		$auth = Zend_Auth::getInstance();
		return $auth->hasIdentity();
	}
	
	protected function setViewMetaData($contentObject)
	{
		$optionsModel = Publicada_Model_Options::getInstance();
		
		$siteNameOption = $optionsModel->getByCode('site_name');
		$siteName = $siteNameOption ? $siteNameOption->getValue() : '';
		$this->view->siteName		= $siteName;
		
		if (!empty($contentObject->title)) {
			$this->view->title = $contentObject->title . ' '
									. self::DEFAULT_TITLE_CONNECTOR . ' '
									. $siteName;
		}
		elseif (!empty($contentObject->name)) {
			$this->view->title = $contentObject->name . ' '
									. self::DEFAULT_TITLE_CONNECTOR . ' '
									. $siteName;
		}
		else {
			$this->view->title = $siteName;
		}

		try {
			$this->view->description = isset($contentObject) ? $contentObject->description : '';
		}
		catch (Exception $e) {
			$this->view->description = '';
		}

		try {
			$this->view->keywords = isset($contentObject) ? $contentObject->keywords : '';
		}
		catch (Exception $e) {
			$this->view->keywords = '';
		}
	}
	
	public function getModuleDirectory()
	{
		return Zend_Controller_Front::getInstance()->getModuleDirectory();
	}
	
	public function getUserLocale()
	{
		if (null === $this->_getParam('lang')) {
			$configuration = Zend_Registry::get('configuration');
			return $configuration['languages']['1'];
		}
		else {
			return $this->_getParam('lang');
		}
	}
	
	public function preDispatch()
	{
		/*
		 * User and content locales
		 */
		$userLocale = $this->getUserLocale();
		Zend_Registry::set('current_user_locale', $userLocale);
		Zend_Registry::set('content_locale', $userLocale);
		Zend_Registry::set('Zend_Locale', $userLocale);
		
		/*
		 * Translate functionality
		 */
		$translate = new Zend_Translate('ini', $this->getModuleDirectory() . '/i18n', null, array('scan' => Zend_Translate::LOCALE_DIRECTORY));
		$translate->setLocale($userLocale);
		Zend_Registry::set('Zend_Translate', $translate);

		/*
		 * Google Analytics filter
		 */
		Zend_Registry::set('nogareport', $this->_getParam('nogareport'));

		/*
		 * Modify response headers
		 */
		$this->getResponse()->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0', true);
		$this->getResponse()->setHeader('Pragma', 'no-cache', true);
		$this->getResponse()->setHeader('Content-Type', 'text/html; charset=utf-8');
		$this->getResponse()->setHeader('Content-Language', Zend_Registry::get('content_locale'));

		/*
		 *  Provide logger
		 */
		$date = new Zend_Date();
		$currentDate = $date->toString('YYYYMMdd');
		$configuration = Zend_Registry::get('configuration');
		$todaysLogFile = $configuration['logpath'] . '/' . APPLICATION_ENV . '_log_' . $currentDate . '.log';
		$writer = new Zend_Log_Writer_Stream($todaysLogFile);
		$logger = new Zend_Log($writer);
		Zend_Registry::set('actionLogger', $logger);
	}

	public function postDispatch()
	{
		/*
		 * Log requests
		 */
// 		TODO: fix this
// 		$logger = Zend_Registry::get('actionLogger');
// 		$logger->info($this->getRequest()->getServer('REQUEST_URI') . ' | ' . $this->getRequest()->getServer('HTTP_USER_AGENT') . ' | ' . $this->getRequest()->getServer('REMOTE_ADDR'));
	}
}
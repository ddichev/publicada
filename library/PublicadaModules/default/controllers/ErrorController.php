<?php

class ErrorController extends Publicada_Controller_Action
{
  public function errorAction()
  {
    $this->_helper->layout->setLayout('layouts/general/default');

    $configuration = Zend_Registry::get('configuration');
// 		$actionLogger = Zend_Registry::get('actionLogger');

    $pageModel = Publicada_Model_Pages::getInstance();
    $mainPage = $pageModel->getByCode(Publicada_Model_Page::DEFAULT_ROOT_PAGE_CODE);
    $this->view->mainPages = $mainPage->getPublishedChildren();

    $this->setViewMetaData($this->view->page);

    $errors = $this->_getParam('error_handler');

    $this->view->exception = $errors->exception;
// 		$actionLogger->warn($errors->exception->getMessage() . "\n" .
// 			$errors->exception->getTraceAsString() . "\n"); 

    switch ($errors->type) {
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:

      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER:

      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        $this->getResponse()
          ->setRawHeader('HTTP/1.1 404 Not Found');
        $this->view->title = '404';
        break;

      default:
        $this->getResponse()
          ->setRawHeader('HTTP/1.1 500 Internal Server Error');
        $this->view->title = '500';
        break;
    }

    if (APPLICATION_ENV == 'production') {
      $this->view->showErrors = false;
    } else {
      $this->view->showErrors = true;
    }
  }
}
<?php
class Publicada_IndexController extends Publicada_Controller_Management
{
  public function indexAction()
  {
    // TODO: add default stuff here in index action of publicada controller
  }

  public function clearcacheAction()
  {
    $cacheConfig = new Zend_Config(require APPLICATION_PATH . '/configs/cache.php');
    $frontendOptions = $cacheConfig->frontendOptions->toArray();
    $backendOptions = $cacheConfig->backendOptions->toArray();

    $cache = Zend_Cache::factory('Page',
      'File',
      $frontendOptions,
      $backendOptions);
    $cache->clean(Zend_Cache::CLEANING_MODE_ALL);
    $this->_redirect($this->view->url(array('action' => 'index', 'cl' => Zend_Registry::get('content_locale'))), array('prependBase' => false));

  }
}
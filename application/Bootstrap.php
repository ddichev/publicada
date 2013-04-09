<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
  protected function _initConfig()
  {
    $this->bootstrap('siteconfig');
    Zend_Registry::set('configuration', $this->getResource('siteconfig'));
  }

  protected function _initCache()
  {
    $configuration = Zend_Registry::get('configuration');
    if ($configuration['cache_enabled']) {

      $cacheConfig = new Zend_Config(
        require 'configs/cache.php');
      $frontendOptions = $cacheConfig->frontendOptions->toArray();
      $backendOptions = $cacheConfig->backendOptions->toArray();

      if (APPLICATION_ENV == 'test' || APPLICATION_ENV == 'development') {
        $frontendOptions['debug_header'] = true;
      }

      $cache = Zend_Cache::factory('Page', 'File', $frontendOptions, $backendOptions);
      $cache->start();
    }
  }

  protected function _initAutoload()
  {
    $autoloader = Zend_Loader_Autoloader::getInstance();
    $autoloader->registerNamespace(array('Publicada', 'Scienta'));
    return $autoloader;
  }

  protected function _initDatabase()
  {
    $this->bootstrap('db');
    $db = $this->getResource('db');
    $db->setFetchMode(Zend_Db::FETCH_OBJ);
    Zend_Registry::set('db', $db);

    $tables = $db->listTables();

    if (count($tables) < 10) {
      $db->getConnection()->exec(file_get_contents(APPLICATION_PATH . '/../writable/data/dbinit.sql'));
    }

  }

  protected function _initZFDebug()
  {
    if (APPLICATION_ENV == 'development') {
      $autoloader = Zend_Loader_Autoloader::getInstance();
      $autoloader->registerNamespace('ZFDebug');

      $options = array(
        'plugins' => array(
          'Variables',
          'Database' => array(
            'adapter' => Zend_Registry::get('db')
          ),
          'Memory',
          'Time',
          'Registry',
          'Exception'
        )
      );
      $debug = new ZFDebug_Controller_Plugin_Debug($options);

      $this->bootstrap('frontController');
      $frontController = $this->getResource('frontController');
      $frontController->registerPlugin($debug);
    }
  }

}

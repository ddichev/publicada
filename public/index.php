<?php

defined('LIBRARY_PATH')
    || define('LIBRARY_PATH', (getenv('LIBRARY_PATH') ? getenv('LIBRARY_PATH') : realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'library')));

defined('APPLICATION_PATH')
	|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .  'application'));

defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

set_include_path('.' . PATH_SEPARATOR . LIBRARY_PATH .
					   PATH_SEPARATOR . get_include_path());

require_once 'Zend/Application.php';  

$application = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();
$application->run();

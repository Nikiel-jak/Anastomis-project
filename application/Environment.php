<?php

defined('ROOT_PATH') || define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));

defined('PUBLIC_PATH') || define('PUBLIC_PATH', realpath(ROOT_PATH . '/public'));

defined('LIB_PATH') || define('LIB_PATH', realpath(ROOT_PATH . '/library'));

defined('UPLOAD_PATH') || define('UPLOAD_PATH', realpath(PUBLIC_PATH . '/upload'));

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__)));

defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
    
defined('CRON_PROCESS') || define('CRON_PROCESS', false);
error_reporting(E_ALL|E_NOTICE);

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(ROOT_PATH . '/library'),
    get_include_path(),
)));
/** Zend_Application */
require_once 'Orion/Application.php';  

// Create application
$application = new Orion_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . '/configs/application.ini'
);
<?php
require dirname(__FILE__).'/../../application/Environment.php';
$application->bootstrap(array(
     'autoloader', 'config', 'db', 'smtp', 'view' , 'routing','log'
));
$config = new Zend_Config_Ini(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'application.ini', APPLICATION_ENV);
$dbUsername = $config->resources->db->params->username;
$dbPassword = $config->resources->db->params->password;
$dbName = $config->resources->db->params->dbname;
$dbHost = $config->resources->db->params->host;
$dbPort = $config->resources->db->params->port;
$file = ROOT_PATH . '/var/backup/' . time() . '.sql';
$command = sprintf("
    mysqldump -u %s -P %s -p%s -h %s -d %s --skip-no-data > %s",
    escapeshellcmd($dbUsername),
    escapeshellcmd($dbPort),
    escapeshellcmd($dbPassword),
    escapeshellcmd($dbHost),
    escapeshellcmd($dbName),
    escapeshellcmd($file)
);

exec($command);
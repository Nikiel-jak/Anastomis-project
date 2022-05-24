<?php
/**
 * @author k.nikiel
 *
 * Cron odpowiadający za wysyłkę maili 
 */
require dirname(__FILE__).'/../../application/Environment.php';
$application->bootstrap(array(
     'autoloader', 'config', 'db', 'smtp', 'view' , 'routing'
));

$mailQueue = new App_Model_MailSendQueue_DbTable();
$entries = $mailQueue->fetchEntriesToSend();
if(count($entries) > 0) {
    foreach($entries as $entry) {
        $entry->send();
    }
} else {
    echo "No email to send";
}

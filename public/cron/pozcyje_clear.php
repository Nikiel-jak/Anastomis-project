<?php
require dirname(__FILE__).'/../../application/Environment.php';
$application->bootstrap(array(
     'autoloader', 'config', 'db', 'smtp', 'view' , 'routing','log'
));
$dir = ROOT_PATH.'/var/imports/';
$dir_sucess = ROOT_PATH.'/var/imports_success/';
$dir_duplicate= ROOT_PATH.'/var/imports_duplicate/';
$rqport_quantity_all = 0;
$raport_duplicate= 0;
$rqport_success = 0;
$model = new App_Model_Declaration_DbTable();
try{
    $reulsts_to_copy = $model->clearSameDeclaration();
    foreach($reulsts_to_copy as $dec){
        $rqport_quantity_all++;
         $file = $dec->works.'_Z_'.$dec->year.'_'.$dec->number.'_'.$dec->lp.'.CSV';
         if(file_exists($dir_sucess.$file )){
            copy ($dir_sucess.$file , $dir.$file );
            unlink($dir_sucess.$file);
            $rqport_success++;
         } elseif(file_exists($dir_duplicate.$file )){
            copy ($dir_duplicate.$file , $dir.$file );
            unlink($dir_duplicate.$file );
            $raport_duplicate++;
         }
    }
} catch (Exception $e){
    die('ERROR');
}
if(php_sapi_name() != 'cli' ){
    echo 'RAPORT:<br/>';
    echo 'Wyczyszczonych rekordów: '.$rqport_quantity_all.'<br/>';
    echo 'Przeniesionych ponowanie do importu z katlaogu success: '.$rqport_success.'<br/>';
    echo 'Przeniesionych ponowanie do importu z katlaogu duplicate: '.$raport_duplicate.'<br/>';
};
die('success');
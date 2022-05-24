<?php
require dirname(__FILE__).'/../../application/Environment.php';
$application->bootstrap(array(
     'autoloader', 'config', 'db', 'smtp', 'view' , 'routing','log'
));
$count = 1;
$model = new App_Model_Declaration_DbTable();
$to_fix = $model->getAllToFix();
echo 'wszystkich do importu: '.count($to_fix);
echo PHP_EOL;
if(count($to_fix)){
    foreach($to_fix as $declaration){
        echo 'numer: '.$count;
        echo PHP_EOL;
        $pozycje = explode(',' ,$declaration->pozycje);
        $dec = $model->getDeclaration($declaration->number, $declaration->lp, $declaration->year, $declaration->works);
        $dec->helper = 1;
        $dec->save();
        foreach($pozycje as $lp){
            $row = $model->getDeclaration($declaration->number, $lp, $declaration->year, $declaration->works);
            if(!$row){
                $adapter = $model->getAdapter();
                $adapter->beginTransaction();
                try{
                    $insert = $dec->toArray();
                    $insert['lp'] = $lp;
                    $insert['helper'] = 1;
                    $model->setDecalaration($insert);
                    $adapter->commit();
                } catch (Exception $e){
                    echo $e; die();
                    Orion_Log::error($e);
                    $adapter->rollBack();
                }
            } else {
                $row->helper = 1;
                $row->save();
            }
        }
        $count++;
    }
} else {
    die('EVERYTHING IS FINE');
}
die('DONE');
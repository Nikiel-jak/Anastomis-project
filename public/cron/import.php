<?php
require dirname(__FILE__).'/../../application/Environment.php';
$application->bootstrap(array(
     'autoloader', 'config', 'db', 'smtp', 'view' , 'routing','log'
));
$qunatity_to_copy = 1000;
if(php_sapi_name() != 'cli' ){
    $qunatity_to_copy == 20;  
};
$raport_copy = 0;
$raport_duplicate = 0;

$dir = ROOT_PATH.'/var/imports/';
$dir_sucess = ROOT_PATH.'/var/imports_success/';
$dir_errors = ROOT_PATH.'/var/imports_errors/';
$dir_duplicate= ROOT_PATH.'/var/imports_duplicate/';
$dir_stream = opendir($dir);
$count = 1;
$type = 1;
$model = new App_Model_Declaration_DbTable();
while(false !== ($file = readdir($dir_stream))){
    if($count == $qunatity_to_copy){
        break;
    }
    if($file != '.' && $file != '..') {
        if (($handle = fopen($dir.$file, "r")) !== FALSE) {
            $type = 1;
            $data = fgetcsv($handle, 1000, ';','"');
            /*
             * Zmiana ze względu na możliwość występowania średnika w nazwie.
             */
//            $bufor = fgets($handle, 4096);
//            $cos = preg_replace(array('/"/','/\xFE/','/\xFF/'),array('"','',''),$bufor);
//            $data = explode('";"',$cos);
            fclose($handle);
            $name = explode('_',$file);
            $row = $model->getDeclaration($name[3],$name[4],$name[2],$name[0]);
            $count++;
            if($row){
                if(file_exists($dir_duplicate.$file)){
                    unlink($dir.$file);
                    $raport_duplicate++;
                    continue;
                }
                copy ( $dir.$file , $dir_duplicate.$file );
                unlink($dir.$file);
                $raport_duplicate++;
                continue;
            }
            
            $insert = array(
                'helper' => 1,
                'number' => $name[3],
                'works' => $name[0],
                'year' => $name[2],
                'lp' => intval($name[4]),
                'kod_kraju' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[0]),'')),
                'data' => preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[1]),
                'pozycje' => preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[2]),
                'typ_produktu' => preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[3]),
                'nazwa' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[4]))),
                'numer' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[5]))),
                'ognioodpornosc' =>strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[6]))),
                'reakcja_na_ogien' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[7]))),
                'dzialanie_ognia_zewnetrznego' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[8]))),
                'odpornosc_na_uderzenie_pocisku' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[9]))),
                'odpornosc_na_wybuch' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[10]))),
                'odpornosc_przeciwwlamaniowa' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[11]))),
                'odpornosc_na_wahadlowe_uderzenie_ciala' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[12]))),
                'odpornosc_na_nagle_zmiany_temperatury_i_na_roznice_temperatur' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[13]))),
                'odpornosc_na_wiatr_snieg_oraz_obciazenie_trwale_i_przylozone' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[14]))),
                'bezposrednia_izolacyjnosc_od_dzwiekow_powietrznych_rw' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[15]))),
                'bezposrednia_izolacyjnosc_od_dzwiekow_powietrznych_C_Ctr' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[16]))),
                'wlasciwosci_cieplne_wspolczynnik_przenikania_ciepla_1' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[17]))),
                'wlasciwosci_cieplne_wspolczynnik_przenikania_ciepla_2' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[18]))),
                'wspolczynnik_przepuszczalnosci_swiatla' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[19]))),
                'wspolczynnik_dbicia_swiatla_pv_LR' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[20]))),
                'wspolczynnik_odbicia_swiatła_p_v_L_r' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[21]))),
                'wspolczynnik_przepuszczalnosci_bezposredniej_promieniowania_slon' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[22]))),
                'wspolczynnik_odbicia_bezposredniego_promieniowania_Er' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[23]))),
                'wspolczynnik_odbicia_bezposredniego_promieniowania_p_e_E_r' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[24]))),
                'wspolczynnik_calkowitej_przepuszczalnosci_energii_promieniowania' => strip_tags(trim(preg_replace(array('/;/','/"/','/\xFE/','/\xFF/'),array(''),@$data[25]))),
            );
            $adapter = $model->getAdapter();
            if($type == 1){
                $adapter->beginTransaction();
                try{
                    $model->setDecalaration($insert);
                    $raport_copy++;
                    $adapter->commit();
                } catch (Exception $e){
                    $type = 0;
                    Orion_Log::error($e);
                    $adapter->rollBack();
                }
            }
        }
        
        if($type === 1){
            copy ( $dir.$file , $dir_sucess.$file );
        } elseif($type === 0) {
            copy ( $dir.$file , $dir_errors.$file );
        }
        unlink($dir.$file);
    }
    
}
if(php_sapi_name() != 'cli' ){
    echo 'RAPORT:<br/>';
    echo 'Zaimportowanych deklaracji: '.$raport_copy.'<br/>';
    echo 'Zduplikowanych deklaracji: '.$raport_duplicate.'<br/>';
};
die('DONE');
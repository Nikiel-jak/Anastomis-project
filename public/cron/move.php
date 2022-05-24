<?php
require dirname(__FILE__).'/../../application/Environment.php';
$application->bootstrap(array(
     'autoloader', 'config', 'db', 'smtp', 'view' , 'routing','log'
));
$dir_sucess = ROOT_PATH.'/var/imports_success/';
$dir_duplicate= ROOT_PATH.'/var/imports_duplicate/';
$model = new App_Model_Declaration_DbTable();
$to_fix = $model->getAllToMoveFix();
if(count($to_fix)){
    foreach($to_fix as $declaration){
        $insert = array();
         $file = $declaration->works.'_Z_'.$declaration->year.'_'.$declaration->number.'_'.$declaration->lp.'.CSV';
         if(file_exists($dir_sucess.$file )){
             $handle = fopen($dir_sucess.$file, "r");
         } elseif(file_exists($dir_duplicate.$file )) {
             $handle = fopen($dir_duplicate.$file, "r");
         }
         if($handle){
            $data = fgetcsv($handle, 1000, ';','"');
         }
         if(count($data)){
               $insert = array(
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
                'bezposrednia_izolacyjnosc_od_dzwiekow_powietrznych_C_Ctr' => '('.strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/','/\(/'),array('','','',''),@$data[16]))).', '.strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/','/\)/'),array('','','',''),@$data[17]))).')',
                'wlasciwosci_cieplne_wspolczynnik_przenikania_ciepla_1' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[18]))),
                'wlasciwosci_cieplne_wspolczynnik_przenikania_ciepla_2' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[19]))),
                'wspolczynnik_przepuszczalnosci_swiatla' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[20]))),
                'wspolczynnik_dbicia_swiatla_pv_LR' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[21]))),
                'wspolczynnik_odbicia_swiatła_p_v_L_r' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[22]))),
                'wspolczynnik_przepuszczalnosci_bezposredniej_promieniowania_slon' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[23]))),
                'wspolczynnik_odbicia_bezposredniego_promieniowania_Er' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[24]))),
                'wspolczynnik_odbicia_bezposredniego_promieniowania_p_e_E_r' => strip_tags(trim(preg_replace(array('/"/','/\xFE/','/\xFF/'),array('','',''),@$data[25]))),
                'wspolczynnik_calkowitej_przepuszczalnosci_energii_promieniowania' => strip_tags(trim(preg_replace(array('/;/','/"/','/\xFE/','/\xFF/'),array(''),@$data[26]))),
            );
         }
         if(count($insert)){
            $adapter = $model->getAdapter();
            $adapter->beginTransaction();
            try{
                $where = 'number = '.$declaration->number.' AND works  = '.$declaration->works.' AND year = '.$declaration->year.' AND lp = '.$declaration->lp;
                 $model->update($insert, $where);
                $adapter->commit();
            } catch (Exception $e){
                $adapter->rollBack();
            }
         }
    }
} else {
    die('EVERYTHING IS FINE');
}
die('DONE');
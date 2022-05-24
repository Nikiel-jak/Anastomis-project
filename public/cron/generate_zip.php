<?php
require dirname(__FILE__).'/../../application/Environment.php';
$application->bootstrap(array(
     'autoloader', 'config', 'db', 'smtp', 'view' , 'routing','log','session'
));

$view = new Zend_View();
$view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'View_Helper')
     ->addHelperPath('Orion/View/Helper','Orion_View_Helper')
     ->addScriptPath(APPLICATION_PATH . '/modules/site/views/scripts');
require_once(LIB_PATH.'/html2pdf/html2pdf.class.php');
$model = new App_Model_Download_DbTable();
$declarationModel = new App_Model_Declaration_DbTable();

$row_download = $model->getOneToGenerate();
if(!$row_download){
    die('nothing to do');
}
echo 'Rozpoczeto generowanie zipa'.PHP_EOL;
$row_download->expired_at = new Zend_Db_Expr('NOW() + INTERVAL 3 DAY');
$data = unserialize($row_download->data);
$hash = $row_download->hash;
$path = PUBLIC_PATH.'/upload/declaration/'.$hash.'/';
$set_lang = $data['zip'];
$lang = (array_shift($set_lang));

$language = new Orion_Language();
$language->setLanguageByPrefix($lang);
$language->setLanguageContent($language->getPagePrefix());
$translate = new Zend_Translate(array('adapter' => Zend_Translate::AN_CSV,'disableNotices' => true));
$translate->addTranslation(APPLICATION_PATH . '/languages/'.Orion_Language::getPagePrefix().'/common', Orion_Language::getPagePrefix());
$translate->addTranslation(APPLICATION_PATH . '/languages/'.Orion_Language::getPagePrefix().'/site', Orion_Language::getPagePrefix());  
if($translate->isAvailable(Orion_Language::getPagePrefix())) {
    $translate->setLocale(Orion_Language::getPagePrefix()); 
    Zend_Registry::set('Zend_Translate', $translate);
}
if(!is_dir($path) && !is_writeable($path)){
    echo 'Tworze katalogu dla zipa'.PHP_EOL;
    mkdir($path, 0777, true);
}
if(!empty($row_download->made_declarations) && $row_download->made_declarations != NULL){
    $files_name = unserialize($row_download->made_declarations);
} else {
    $files_name = array();
}
$z = 0;
foreach($data['zip'] as $key => $lang ){
    if($z > 8){
        $row_download->made_declarations = serialize($files_name);
        $row_download->save();
        die('PARTLY GENERATE');
    } 
    
    $declaration_data = explode('_', $key);
    $file_name = $declaration_data['0'].'_'.$declaration_data['1'].'_'.$declaration_data['2'].'_'.$declaration_data['4'].'.pdf';
    if(is_array($files_name) && !in_array($path.$file_name, $files_name)){
        $z++;
    } else {
        echo 'Dalej:'.$file_name.PHP_EOL;
        continue; 
    }
    $row = $declarationModel->getDeclaration($declaration_data['0'],$declaration_data['3'],$declaration_data['2'],$declaration_data['1']);
    
    if(!$row){
        continue;
    }
    echo 'Stworzono deklaracje:'.$declaration_data['0'].'_'.$declaration_data['1'].'_'.$declaration_data['2'].'_'.$declaration_data['4'].PHP_EOL;
    $html2pdf = new HTML2PDF('L', 'A4', 'en', true, 'UTF-8', 0);
    $type = (preg_replace('/[^0-9]/', '', $row->typ_produktu));
    switch($type){
        case '0':
            $html = $view->partial('helpers/declaration_0.phtml',array(
                'row' => $row, 'this' => $view,
                'number' =>'EN 1279-5:2005+A2:2010',
                'branches' => array(
                    'NOWA',
                    'TCZEW',
                    'TYCHY',
                    'RADOMSKO',
                ),
                'title' => 'DECLARATION_HEADER_0',
                'notified_unit' => 'ift_rosenheim',
            ));
        break;
        case '1':
            $html = $view->partial('helpers/declaration_1.phtml',array(
                'row' => $row, 'this' => $view,
                'number' =>'EN 572, EN 1096',
                'branches' => array(
                    'NOWA',
                    'TCZEW',
                    'TYCHY',
                    'RADOMSKO',
                ),
                'title' => 'DECLARATION_HEADER_1',
                'notified_unit' => 'ift_rosenheim',
            ));
        break;
        case '2':
            $html = $view->partial('helpers/declaration_2.phtml',array(
                'row' => $row, 'this' => $view,
                'number' =>'EN 12150-2:2005',
                'branches' => array(
                    'TCZEW',
                    'TYCHY',
                    'RADOMSKO',
                ),
                'title' => 'DECLARATION_HEADER_2',
                'notified_unit' => 'institute_of_glass',
            ));
        break;
        case '3':
            $html = $view->partial('helpers/declaration_3.phtml',array(
                'row' => $row, 'this' => $view,
                'number' =>'EN 12150-2:2005',
                'branches' => array(
                    'TYCHY',
                ),
                'title' => 'DECLARATION_HEADER_3',
                'notified_unit' => 'institute_of_glass',
            ));
        break;
        case '4':
            $html = $view->partial('helpers/declaration_4.phtml',array(
                'row' => $row, 'this' => $view,
                'number' =>'EN 14179:2005',
                'branches' => array(
                    'TYCHY',
                ),
                'title' => 'DECLARATION_HEADER_4',
                'notified_unit' => 'ift_rosenheim',
            ));
        break;
        case '5':
            $html = $view->partial('helpers/declaration_5.phtml',array(
                'row' => $row, 'this' => $view,
                'number' =>'EN 14179:2005',
                'branches' => array(
                    'TYCHY',
                ),
                'title' => 'DECLARATION_HEADER_5',
                'notified_unit' => 'tsus',
            ));
        break;
        case '6':
            $html = $view->partial('helpers/declaration_6.phtml',array(
                'row' => $row, 'this' => $view,
                'number' =>'EN 1863-2:2004',
                'branches' => array(
                    'TYCHY',
                ),
                'title' => 'DECLARATION_HEADER_6',
                'notified_unit' => 'institute_of_glass',
            ));
        break;
        case '7':
            $html = $view->partial('helpers/declaration_7.phtml',array(
                'row' => $row, 'this' => $view,
                'number' =>'EN 14449:2005',
                'branches' => array(
                    'TYCHY',
                ),
                'title' => 'DECLARATION_HEADER_7',
                'notified_unit' => 'ift_rosenheim',
            ));
        break;
        case '8':
            $html = $view->partial('helpers/declaration_8.phtml',array(
                'row' => $row, 'this' => $view,
                'number' =>'EN 14449:2005',
                'branches' => array(
                    'NOWA',
                    'TCZEW',
                    'TYCHY',
                    'RADOMSKO',
                ),
                'title' => 'DECLARATION_HEADER_8',
                'notified_unit' => 'ift_rosenheim',
            ));
        break;
        case '9':
            $html = $view->partial('helpers/declaration_9.phtml',array(
                'row' => $row, 'this' => $view,
                'number' =>'EN 1863-2:2004',
                'branches' => array(
                    'TYCHY',
                ),
                'title' => 'DECLARATION_HEADER_9',
                'notified_unit' => 'institute_of_glass',
            ));
        break;
        case '10':
            $html = $view->partial('helpers/declaration_10.phtml',array(
                'row' => $row, 'this' => $view,
                'number' =>'EN 14449:2005',
                'branches' => array(
                    'TCZEW',
                    'TYCHY',
                ),
                'title' => 'DECLARATION_HEADER_10',
                'notified_unit' => 'exova_warringtonfire',
            ));
        break;
        default:
            $html = $view->partial('helpers/declaration_0.phtml',array(
                'row' => $row, 'this' => $view,
                'number' =>'EN 1279-5:2005+A2:2010',
                'branches' => array(
                    'NOWA',
                    'TCZEW',
                    'TYCHY',
                    'RADOMSKO',
                ),
                'title' => 'DECLARATION_HEADER_0',
                'notified_unit' => 'ift_rosenheim',
            ));
        break;
    }
    $html2pdf->setDefaultFont('freesans');
    $html2pdf->writeHTML($html);

    $files_name[] = $path.$file_name;
    $html2pdf->Output($path.$file_name,'F');
}
if($files_name != FALSE && count($files_name)){
    $declarationModel->create_zip($files_name, $path.$declaration_data['0'].'_'.$declaration_data['1'].'_'.$declaration_data['2'].'.zip');
    
    $i =1;
    foreach($files_name as $file){
        $i++;
        if(file_exists($file)){
            unlink($file);
        }
    }
}
echo 'Usunietych deklaracji:'.$i.PHP_EOL;
echo 'Wysylka maila na ares:'.$row_download->email.PHP_EOL;

$configurationModel = new App_Model_Configuration_DbTable();
$configurationModel->getValue('site_name');
$link = 'http://'.$configurationModel->getValue('site_name').$view->url(array('lang' => $lang, 'hash' => $hash),'site-zip-download');
//$link = 'http://deklaracje.x25.pl/upload/declaration/'.$hash.'/'.$declaration_data['0'].'_'.$declaration_data['1'].'_'.$declaration_data['2'].'.zip';

$html = $view->translate('MAIL_TEXT_DECLARATION', date('Y-m-d H:i', time()));
$html .='<br/>';
$html .= $view->translate('MAIL_TEXT_LINK', $link);
$html .='<br/><br/>';
$html .= $view->translate('MAIL_TEXT_THANKS');
$html .= '<br/><br/>';

$html .= 'PRESS GLASS SA<br/>
www.pressglass.eu<br/><br/>
PRESS GLASS SA • Nowa Wieś, ul. Kopalniana 9 • 42-262 Poczesna<br/>
NIP 574-000-74-96 • tel. +48 34 327 50 69 • faks +48 34 327 58 01<br/><br/>
 
KRS 0000131477 • Sąd Rejonowy w Częstochowie, XVII Wydział Gospodarczy <br/>
Kapitał zakładowy 500 000,00 zł opłacony w całości<br/><br/>Chroń środowisko, przed wydrukowaniem wiadomości rozważ czy jest to konieczne';
$mail = new Zend_Mail('UTF-8');
$mail->addTo($row_download->email)
      ->addBcc('info@tab2.pl')
      ->setFrom('cewww@pressglass.eu','PRESS GLASS SA')
      ->setSubject($view->translate('MAIL_TEXT_TITLE').$declaration_data['0'])
      ->setBodyHtml($html)
      ->send();
$row_download->status = 1;
$row_download->lang  = $lang;
$row_download->file_name = $declaration_data['0'].'_'.$declaration_data['1'].'_'.$declaration_data['2'].'.zip';
$row_download->save();
die('DONE');
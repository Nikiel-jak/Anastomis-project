<?php

class Site_IndexController extends Zend_Controller_Action {
	
    public function indexAction(){   
				
        $type = $this->_getParam('type','normal');
        $device = $this->_getParam('device','ios');
        $this->view->lang = $this->_getParam('lang');
        $this->view->show_in_us = $this->_getParam('show_in_us', 0);
        $data = array();
        $model = new App_Model_Attributes_DbTable();
		
		/**
		 * Pobierz wszystkie dane do suwaków.
		 */
        $results = $model->getGroupWithAttributesToSite($this->view->lang);
				
        $modelValues = new App_Model_Attributes_Values_DbTable();
        $values = array();
        foreach($results as $row){
            $row['min_value'] = preg_replace(array('/\.00/', '/\.0/'), '', $row['min_value']);
            $row['max_value'] = preg_replace(array('/\.00/', '/\.0/'), '', $row['max_value']);
            $data[$row->order][$row->id] = $row;
            if($row->type == 1){
                $val = $modelValues->getActiveToFormByAttribute($row->id);

                if($val){
                    $values[$row->id] = $val;
                }
            }
        }
        ksort($data);
        
        $model_atr = new App_Model_Anastomosis_Attributes_DbTable();
        $attributes_realtion = $model_atr->getAttributesRelation();
        $this->view->attributes_realtion = $attributes_realtion;

        $this->view->type = $type;
        $this->view->values = $values;
        $this->view->device = $device;
        $this->view->group = $data;        
    }
    
	/**
	 * Metody wywoływane przez Ajax.
	 * Wyszukiwarka.
	 * @return type
	 */
    public function ajaxAction() {
		
        $request = $this->getRequest();
        $model = new App_Model_Anastomosis_Attributes_DbTable();
        $jsonData = $model->search($request->getPost());
					
        return $this->_helper->json->sendJson($jsonData);
    }
    
	
    public function mobileAction() {
				
        $request = $this->getRequest();
        $modelAttributes = new App_Model_Attributes_DbTable();
        $model = new App_Model_Anastomosis_Attributes_DbTable();
        $results = $modelAttributes->getGroupWithAttributesToSite($this->view->lang);
        $search = $model->search($_GET);
        foreach($results as $row){
            $data[$row->order][] = $row; 
        }
        ksort($data);
        ksort($search);
        ob_end_clean();
        $html = $this->view->partial('helpers/partials/pdf.phtml',array('group' => $data, 'search' => $search, 'post' => $_GET));
        require_once(LIB_PATH.'/html2pdf/html2pdf.class.php');
        $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8', 0);
        $html2pdf->AddFont('myriadprosemicn', '', LIB_PATH.'/html2pdf/_tcpdf_5.0.002/fonts/myriadpro.php');
        $html2pdf->AddFont('myriadprosemicnb', '', LIB_PATH.'/html2pdf/_tcpdf_5.0.002/fonts/myriadpro.php');
        $html2pdf->setDefaultFont('myriadprosemicn');
        $html2pdf->writeHTML($html);
        $html2pdf->Output($this->view->translate('SITE_PDF_DOWNLOAD_NAME').'_'.date('Y_m_d').'.pdf','D');
        die();
    }
    
	/**
	 * Generuj PDF
	 */
    public function pdfAction() {
		
		$this->view->lang = $this->_getParam('lang');

        $request = $this->getRequest();
        $model = new App_Model_Anastomosis_Attributes_DbTable();
        $post = $request->getPost();
        $serach = $model->search($post);
        $modelAttributes = new App_Model_Attributes_DbTable();        
        $modelAttributesValues = new App_Model_Attributes_Values_DbTable();        
        $results = $modelAttributes->getGroupWithAttributesToSite($this->view->lang);	
		
        foreach($results as $row){
            $data[$row->order][] = $row; 
        }

        ksort($data);
        ksort($serach);
        ob_end_clean();
        require_once(LIB_PATH.'/html2pdf/html2pdf.class.php');
        $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8', 0);
        $html2pdf->setDefaultFont('freesans');
        foreach ($post as $k_post => $v_post) {
            if (in_array($k_post, array(40, 41))) {
                $values = $modelAttributesValues->getActiveToFormByAttribute($k_post);
                $post[$k_post]['max'] = $values[$post[$k_post]['max']];
                $post[$k_post]['min'] = $values[$post[$k_post]['min']];
            }
            
            if ($k_post == 39) {
                $values = $modelAttributesValues->getActiveToFormByAttribute($k_post);
                $post[$k_post] = $values[$post[$k_post]];
            }
        }
        $html = $this->view->partial('helpers/partials/pdf.phtml',array('group' => $data, 'search' => $serach, 'post' => $post,'lang'=>$this->view->lang));
        		
		$html2pdf->AddFont('myriadprosemicn', '', LIB_PATH.'/html2pdf/_tcpdf_5.0.002/fonts/myriadpro.php');
        $html2pdf->AddFont('myriadprosemicnb', '', LIB_PATH.'/html2pdf/_tcpdf_5.0.002/fonts/myriadpro.php');
        $html2pdf->setDefaultFont('myriadprosemicn');
		
        $html2pdf->writeHTML($html);
        $html2pdf->Output($this->view->translate('SITE_PDF_DOWNLOAD_NAME').'_'.date('Y_m_d').'.pdf','D');
        die();
    }
}

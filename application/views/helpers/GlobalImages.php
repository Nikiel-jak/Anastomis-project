<?php

class View_Helper_GlobalImages extends Zend_View_Helper_Abstract 
{
   
    public function globalImages($type)
    {
        $configurationModel = new App_Model_Configuration_DbTable();
        $image = $configurationModel->getValue($type);
        if(!$image){
            return false;
        }
        $baseurl = $this->view->baseUrl();
        return $baseurl.'/upload/images/global/'.$image;
    }
}
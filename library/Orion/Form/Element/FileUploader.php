<?php
class Orion_Form_Element_FileUploader extends Zend_Form_Element
{
    public $helper = 'fileUploader';
    
     public function init()
    {
        $view = $this->getView();
        $view->addHelperPath(LIB_PATH.'/Orion/View/Helper', 'Orion_View_Helper');
    }
    
   
}
<?php
class Orion_Plugins_LayoutSwitcher extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request) 
    {   
        $layout = Zend_Layout::startMvc();
        $layout->setLayoutPath(Zend_Controller_Front::getInstance()->getModuleDirectory($request->getModuleName()) . '/layouts');
        if($request->isXmlHttpRequest()){
            $layout->disableLayout();

        }
    }   

}

<?php

class View_Helper_IsCurrentLink extends Zend_View_Helper_Abstract 
{
    public function isCurrentLink($routing = ''){
        $router = Zend_Controller_Front::getInstance()->getRouter();
//        if(stristr($router->getCurrentRouteName(),$routing) !== FALSE){
//            return true; 
//        }
        $data = explode('-',$routing);
        $current = explode('-',$router->getCurrentRouteName());
        if($current[0].'-'.@$current[1] == $data[0].'-'.$data[1]){
            return true;
        }
        return false;
    }
}
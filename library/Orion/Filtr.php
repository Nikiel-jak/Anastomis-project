<?php 
class Orion_Filtr extends Zend_Controller_Plugin_Abstract
{
    public function prepare($params) 
    {
        $routing = new Zend_Session_Namespace(Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName());
        foreach ($params as $key => $param){
            if (!in_array($key, array('action','module','controller'))){
                if($param != ''){
                    $data[$key] = $param;
                }
            }
        }
        if(is_array(@$data)){
            $routing->filters = $data;
        } else {
           $routing->unsetAll(); 
        }
    }
    
    public function filtr($sql)
    {
        $routing = new Zend_Session_Namespace(Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName());
        if(is_array($routing->filters)){
            foreach ($routing->filters as $key =>  $filter){
                $sql->where($key. ' LIKE "%'.$filter.'%"');
            }
        }

        return $sql;
    }
    
    public function filtrBetter($sql)
    {
        $routing = new Zend_Session_Namespace(Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName());
        if(is_array($routing->filters)){
            foreach ($routing->filters as $key =>  $filter){
                $sql->where($key. ' LIKE "'.$filter.'"');
            }
        }

        return $sql;
    }    
    public function GetActiveFilter()
    {
        $routing = new Zend_Session_Namespace(Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName());
        if(is_array($routing->filters)){
            return $routing->filters;
        } 
        return false;
    }
}
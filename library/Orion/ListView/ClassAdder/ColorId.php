<?php

class Orion_ListView_ClassAdder_ColorId extends Orion_ListView_ClassAdder
{
    protected $_statuses;
    
    public function __construct($options = array()) {
        if(count($options))
            $this->_statuses = $options;
    }
    
    public function addClasses($value)
    {
        if($value == 'EHM') return array('rectangle_red');
        else if($value == 'EHZ') return array('rectangle_blue');
        else if($this->_statuses){
            if(in_array($value, $this->_statuses))
                return array("rectangle_blue");
            else return array("rectangle_red");
        }
        return array();
    }
}

?>

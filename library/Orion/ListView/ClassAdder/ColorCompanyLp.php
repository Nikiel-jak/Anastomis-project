<?php

class Orion_ListView_ClassAdder_ColorCompanyLp extends Orion_ListView_ClassAdder
{
    protected $_colname;
    
    public function __construct($colname)
    {
        parent::__construct(array($colname => '{' . $colname . '}'));
        $this->_colname = $colname;
    }
    
    public function addClasses($value)
    {
        $options = $this->getOptions();
        
        if($options[$this->_colname] == 'EHM') return array('rectangle_red');
        else if($options[$this->_colname] == 'EHZ') return array('rectangle_blue');
        else return array('rectangle_red');
    }
    
}

?>

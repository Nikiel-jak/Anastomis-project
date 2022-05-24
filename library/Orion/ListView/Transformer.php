<?php

abstract class Orion_ListView_Transformer extends Orion_ListView_Helper
{
    protected $_transformedValue;
    
    abstract public function transform($value);
    
    public function render()
    {
        return $this->_transformedValue;
    }
}

?>

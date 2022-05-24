<?php
class Orion_ListView_Transformer_Status extends Orion_ListView_Transformer
{
    public function transform($value)
    {
        $translate = new Zend_View_Helper_Translate();
        $this->_transformedValue = $translate->translate(strtoupper($this->getListViewName()).'_STATUS_'.$value);
    }
}

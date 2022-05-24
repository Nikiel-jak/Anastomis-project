<?php
class Orion_ListView_Transformer_Translate extends Orion_ListView_Transformer
{
    protected $_text;

    public function __construct($text) {
        $this->_text = $text;
    }

    public function transform($value)
    {
        $translate = new Zend_View_Helper_Translate();
        $this->_transformedValue = $translate->translate(strtoupper($this->getListViewName()).'_'. strtoupper($this->_text) .'_'.$value);
    }
}

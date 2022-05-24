<?php
/*transformacja dla danych 'status' zamieniająca wartość statusu na opis */

class Orion_ListView_Transformer_StatusIntegerTranslate extends Orion_ListView_Transformer
{
    protected $_prefix;
    
    public function __construct($prefix = null) {
        $this->_prefix = $prefix;
        parent::__construct();
    }
    
    public function transform($value)
    {
        $view = new Zend_View();
        
        if($value === '---') $this->_transformedValue = '---';
        else $this->_transformedValue = $view->translate(strtoupper($this->_prefix) . $value); 
    }
}

?>

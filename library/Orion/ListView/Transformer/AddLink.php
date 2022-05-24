<?php
/*transformacja dla danych, które mają być linkiem, wymagane parametry: value, url, url_params, tooltip_text*/

class Orion_ListView_Transformer_AddLink extends Orion_ListView_Transformer
{
    protected $_url;
    protected $_url_params;
    protected $_class;
    protected $_tooltip;
    protected $_translates;

    public function __construct($url, $url_params, $class = '', $tooltip = '')
    {
        parent::__construct($url_params);
        $this->_url = $url;
        $this->_url_params = $url_params;
        $this->_class = $class;
        $this->_tooltip = $tooltip;
    }
    
    
    public function transform($value)
    {
        $view = new Zend_View_Helper_Url();
        $options = $this->getOptions();
        
        $class = '';
        if($this->_class != '') $class = $this->_class;
        
        if($this->_tooltip != '') $class = $class ." tooltip";
        
        $class = "class = '".$class."'";
        
        $title = "title='".$this->_tooltip."'";
        
        $url = '<a ' . $class . ' href="' . $view->url($options, $this->_url). '"><span '.$title.'>'.$value.'</span></a>';
        $this->_transformedValue = $url;
    }
}

?>

<?php
/*transformacja daty */

class Orion_ListView_Transformer_Date extends Orion_ListView_Transformer
{
    protected $_format;
    protected $_replacement;

    public function __construct($format = null, $replacement = null) {
        $this->_format = ($format) ? $format : 'Y-m-d H:i';
        $this->_replacement = ($replacement) ? $replacement : '------';
        parent::__construct();
    }

    public function transform($value)
    {
        if($value != '0000-00-00' && $value != '---'){
            $this->_transformedValue = date($this->_format, strtotime($value));
        } else {
            $this->_transformedValue = $this->_replacement;
        }
    }
}

?>

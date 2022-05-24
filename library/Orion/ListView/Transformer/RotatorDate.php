<?php
/*transformacja daty */

class Orion_ListView_Transformer_RotatorDate extends Orion_ListView_Transformer
{
    public function transform($value)
    {
        $view = new Zend_View();
        if($value == '---') $this->_transformedValue = $view->translate('COMMON_DATE_NOT_DEFINED');
        else $this->_transformedValue = date('Y-m-d H:i', strtotime($value));
    }
}

?>

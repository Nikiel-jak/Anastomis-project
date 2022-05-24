<?php

class Orion_ListView_Transformer_TipsStatus extends Orion_ListView_Transformer
{
    public function transform($value)
    {
        $view = new Zend_View();
        if((int)$value == 0) $this->_transformedValue = $view->translate('TIPS_STATUS_0');
        else if ((int)$value == 1) $this->_transformedValue = $view->translate('TIPS_STATUS_1');
        return $value;
    }
}

?>

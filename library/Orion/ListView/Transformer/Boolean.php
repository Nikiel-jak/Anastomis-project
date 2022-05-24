<?php

class Orion_ListView_Transformer_Boolean extends Orion_ListView_Transformer
{
    public function transform($value)
    {
        $view = new Zend_View();
        if((int)$value == 0) $this->_transformedValue = $view->translate('LISTVIEW_BOOLEAN_NO');
        else if ((int)$value == 1) $this->_transformedValue = $view->translate('LISTVIEW_BOOLEAN_YES');
        return $value;
    }
}

?>

<?php

class Orion_ListView_Transformer_CategoryName extends Orion_ListView_Transformer
{
    public function transform($value)
    {
        $view = new Zend_View();
        if($value == '---') $this->_transformedValue = $view->translate('COMMON_CATEGORY_MAIN_NAME');
        else $this->_transformedValue = $value;
    }
}

?>

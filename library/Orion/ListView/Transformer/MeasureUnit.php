<?php
/*transformacja dla danych jednostka miary */

class Orion_ListView_Transformer_MeasureUnit extends Orion_ListView_Transformer
{
    public function transform($value)
    {
        $view = new Zend_View();
        $this->_transformedValue = $view->translate('TABLE_PRODUCTS-STORE-STATE_MEASURE_UNIT_' . $value); 
    }
}

?>

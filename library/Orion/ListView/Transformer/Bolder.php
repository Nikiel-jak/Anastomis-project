<?php

class Orion_ListView_Transformer_Bolder extends Orion_ListView_Transformer
{
    public function transform($value)
    {
        $this->_transformedValue = '<strong>' . $value . '</strong>';
    }
}

?>

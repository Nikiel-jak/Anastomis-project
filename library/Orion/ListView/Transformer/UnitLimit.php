<?php
/*transformacja dla danych limit w jednostce dla uzytkownika, kiedy is_limited = true ma ustawić na limit, w przeciwnym wypadku wyswietla "brak limitu" */

class Orion_ListView_Transformer_UnitLimit extends Orion_ListView_Transformer
{
    public function transform($value)
    {   
        $this->_transformedValue = ($this->_row['is_limited']) ? $value : "brak limitu";
    }
}

?>

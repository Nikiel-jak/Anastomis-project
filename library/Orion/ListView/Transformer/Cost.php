<?php
/*transformacja dla danych 'cost', 'price', 'limit' zamieniająca liczbę w postaci 123456.12 na 123 456,12 */

class Orion_ListView_Transformer_Cost extends Orion_ListView_Transformer
{
    public function transform($value, $precision = 2, $separator = ',', $thousands_sep = ' ')
    {
        if($value == '---') 
            $this->_transformedValue = '---';
        else {
            $value = preg_replace("/(?<=\d)(?=(\d{3})+(?!\d))/","&nbsp;",$value);
            $this->_transformedValue = preg_replace("/\./",",", $value); 
        }
    }
}

?>

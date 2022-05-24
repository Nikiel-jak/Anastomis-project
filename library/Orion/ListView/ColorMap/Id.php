<?php

class Orion_ListView_ColorMap_Id extends Orion_ListView_ColorMap {
    
    public function getColor($value)
    {
        if((int)$value % 2 == 0) return 'red';
        else return 'blue';
    }
}

?>

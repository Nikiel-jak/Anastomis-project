<?php

class Orion_ListView_ColorMap_Company extends Orion_ListView_ColorMap {
    
    public function getColor($value)
    {
        if($value == 'EHM') return 'red';
        else if($value == 'EHZ') return 'blue';
    }
}

?>

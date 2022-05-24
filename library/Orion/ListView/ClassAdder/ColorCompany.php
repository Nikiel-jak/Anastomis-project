<?php

class Orion_ListView_ClassAdder_ColorCompany extends Orion_ListView_ClassAdder
{
    public function addClasses($value)
    {
        if($value == 'EHM') return array('red');
        else if($value == 'EHZ') return array('blue');
    }
    
}

?>

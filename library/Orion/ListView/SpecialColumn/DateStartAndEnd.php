<?php

class Orion_ListView_SpecialColumn_DateStartAndEnd extends Orion_ListView_SpecialColumn
{
    
    public function __construct($options)
    {
        parent::__construct($options);   
    }

    public function render()
    {
        $options = $this->getOptions();
        
        $dates = $options['date_start'];
        if($options['date_end'] != '') {
            $dates .= ' - ' . $options['date_end'];
        }
        
        return $dates;
    }
}

?>

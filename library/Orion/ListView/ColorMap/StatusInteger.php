<?php
/* funkcja zwraca kolor dla statusu produktu, status może być aktywny lub niekatywny */

class Orion_ListView_ColorMap_StatusInteger extends Orion_ListView_ColorMap {
    
    protected $_status = array();

    public function __construct(array $status) 
    {
        $this->_status = $status;
        parent::__construct();
    }
    
    public function getColor($value)
    {
        if(in_array($value, $this->_status))
                return "status_active";
        else return "status_inactive";
    }
}

?>

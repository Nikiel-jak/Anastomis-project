<?php
/* funkcja zwraca kolor dla statusu produktu, status może być aktywny lub niekatywny */

class Orion_ListView_ClassAdder_ColorStatusInteger extends Orion_ListView_ClassAdder
{
    protected $_status = array();

    public function __construct(array $status) 
    {
        $this->_status = $status;
        parent::__construct();
    }
    
    public function addClasses($value)
    {
        if(in_array($value, $this->_status))
                return array("status_active");
        else return array("status_inactive");
    }
}

?>

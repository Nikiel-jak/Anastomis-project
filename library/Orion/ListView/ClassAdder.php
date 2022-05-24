<?php

abstract class Orion_ListView_ClassAdder extends Orion_ListView_Helper
{
    // ta metoda musi zwrócić array z stringami z nazwami klas
    abstract public function addClasses($value);
    
    static public function classesToString($arr)
    {
        $result = '';
        foreach($arr as $elem)
        {
            $result .= ($elem . ' ');
        }
        
        return ' ' . trim($result);
    }
}

?>

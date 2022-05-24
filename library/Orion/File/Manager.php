<?php

class Orion_File_Manager
{
    public static function createDir($path)
    {
        if(!is_dir($path)){
            mkdir($path,0777, true);
        }
    }
}

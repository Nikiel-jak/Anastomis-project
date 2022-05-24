<?php 
interface Orion_File_Interface {

    public function getPath();

    public function getExtension();

    public function copy();
}
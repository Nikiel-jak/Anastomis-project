<?php
require_once '../application/Environment.php';

    try
    {
        $application->bootstrap()->run();
    } 
    catch (Exception $exception) 
    {
    header("HTTP/1.0 500 Internal Server Error");
    $kod = 500;
    $opis = 'Internal Server Error';
    include 'error/exception.php';
    exit;
    }
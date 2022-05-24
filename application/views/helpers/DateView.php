<?php

class View_Helper_DateView extends Zend_View_Helper_Abstract
{

    public function dateView($date, $format = 'd-m-Y', $status = null)
    {
        $date_create = @date_create($date);
        $year = @date_format($date_create,'Y');
        $day = @date_format($date_create,'d');
        $month = @date_format($date_create,'m');
        $hour = @date_format($date_create,'H');
        $minutes = @date_format($date_create,'i');
        $seconds = @date_format($date_create,'s');
        
        $view = new Zend_View(); 

        if($year > 0 && $day > 0 && $month > 0) {
            return date_format(date_create($date), $format);   
        } else {
            return $view->translate('COMMON_NO_DATE');
        }
    }
}
<?php

class Orion_Model_Mail extends Zend_Mail
{
    protected $_attachedImages = array('logo.png');
    
    public function render($name, $options, $mailUrl)
    {
        $data = unserialize($options);
        $view = $this->getView();
        $view->assign('mailUrl', $mailUrl);
        foreach ($data as $key => $value){            
            $view->assign($key, $value);
            if($key == 'title'){
                $this->setSubject($value);
            }
        }
        $html = $view->render("template/{$name}.phtml");
        $this->setBodyHtml($html);
        return $this;
    }

    public function getView()
    {
        $this->_view = new Zend_View();
        $this->_view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'View_Helper')
                    ->addHelperPath('Yeti/View/Helper','Yeti_View_Helper')
                    ->addScriptPath(APPLICATION_PATH . '/views/scripts');
        return $this->_view;
    }
    
    public function setSubject($subject)
    {
      //  $subject = @iconv('utf-8','iso-8859-2//IGNORE',$subject);
        return parent::setSubject($subject);
    }

    public function setFrom($email, $name='')
    {
       // $name = @iconv('utf-8','iso-8859-2//IGNORE',$name);
        return parent::setFrom($email, $name);
    }

    public function addTo($email, $name='')
    {
      //  $name = @iconv('utf-8','iso-8859-2//IGNORE',$name);
        return parent::addTo($email, $name);
    }

    public function setBodyText($txt)
    {
      //  $txt = @iconv('utf-8','iso-8859-2//IGNORE',$txt);
        return parent::setBodyText($txt, null , Zend_Mime::ENCODING_7BIT );
    }

    public function setBodyHtml($html)
    {
       // $html = @iconv('utf-8','iso-8859-2//IGNORE',$html);
        return parent::setBodyHtml($html, null , Zend_Mime::ENCODING_7BIT );
    }

}

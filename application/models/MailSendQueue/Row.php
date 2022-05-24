<?php
/**
 * Klasa realizuje zadania związane z wysyłką e-maili 
 */
class App_Model_MailSendQueue_Row extends Orion_Model_Row 
{    
    public function attaImg()
    {
       $data = array( 0 => array('name' => 'logo.png', 'id' => 'logo'),
                   );
        return $data; 
    }
    
    protected $_mailUrl;
    
    protected $_form;

    public function __construct(array $config = array()) {
        $configurationModel = new App_Model_Configuration_DbTable();
        $this->_mailUrl = $configurationModel->getValue('site_name');
        $this->_from = $configurationModel->getValue('smpt_username');
        parent::__construct($config);
    }
    
    public function send()
    {  
        $mail = new Orion_Model_Mail('UTF-8');
        $mail->addTo($this->email);
    
        if($this->newsletter_id){
            $newsletterModel = new App_Model_Newsletter_DbTable();
            $newsletter = $newsletterModel->findById($this->newsletter_id);
            if(!$newsletter){
                return false;
            }
            $newsletter->send_started = new Zend_Db_Expr('NOW()');
            $newsletter->save();
            $options = serialize(array(
                'key_deactivation' => $this->options,
                'title' => $newsletter->title,
                'content' => $newsletter->content,
            ));
            $mail->setFrom($this->_from ,$newsletter->sender);    
        } else {
            $mail->setFrom($this->_from, $this->_mailUrl);
            $options = $this->options;
        }
        try{
            
            $mail->render($this->template, $options, $this->_mailUrl);
            $mail->send();
        } catch (exception $e){
            $this->error = $e;
            $this->repeat = $this->repeat+1;
            $this->save();
            if($this->newsletter_id){
                $newsletter->status = App_Model_Newsletter_DbTable::STATUS_ERROR;
                $newsletter->save();
            }
            return false;
        }
        $this->aquired_at = new Zend_Db_Expr('NOW()'); 
        $this->status = App_Model_MailSendQueue_DbTable::STATUS_SENT;
        $this->save();
        if($this->newsletter_id){
            $newsletter->status = App_Model_Newsletter_DbTable::STATUS_SENT;
            $newsletter->send_finished = new Zend_Db_Expr('NOW()');
            $newsletter->save();
        }
      
    } 
}
<?php 

class App_Model_MailSendQueue_DbTable extends Orion_Model_DbTable 
{
    const PACKAGE_SIZE = 10;
    const PACKAGE_SORT_ORDER = 'priority DESC';

    const STATUS_TO_SEND = 'to_send';
    const STATUS_SENT = 'sent';
    const STATUS_ERROR = 'error';
    const STATUS_REPEAT = 'repeat';

    const PRIORITY_IMPORTANT = 9;
    const PRIORITY_TEST_SEND = 8;
    const PRIORITY_PLAIN_MSG = 0;

    protected $_name = 'mail_send_queue';
    protected $_rowClass= 'App_Model_MailSendQueue_Row';

    public function setEmailToSend(array $data)
    {
        $data['status'] = self::STATUS_TO_SEND;
        $data['created_at'] = new Zend_Db_Expr('NOW()');
        $data['created_by'] = Orion_Auth::getProfileId(); 
        $mail_id = parent::insert($data);
        $configurationModel = new App_Model_Configuration_DbTable();
        if(!$configurationModel->isCron()){
            $this->sendEmail($mail_id);
        } 
        return $mail_id;
    }
    
    public function fetchEntriesToSend()
    {
    	$where = array(
    	    '`status` = ?' => self::STATUS_TO_SEND,
            '`repeat` != ?' => 5,
            '`aquired_at` IS NULL',
            '`lock` = ?' => 0,
    	);
    	return $this->fetchAll($where,self::PACKAGE_SORT_ORDER,self::PACKAGE_SIZE);
    }
    
    public function sendEmail($email_id)
    {
        $email = $this->getById($email_id);
        $email->send();
    }
    
    public function sendNewsletter()
    {
        $tosend = $this->fetchEntriesToSend();
        if($tosend->count() == 0){
                return false; 
        }
        foreach ($tosend as $email){
            $email->send();
        }
    }
    
}

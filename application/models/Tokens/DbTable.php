<?php

class App_Model_Tokens_DbTable extends Orion_Model_DbTable
{
    protected $_name = 'tokens';
    const TOKEN_TYPE_SUBSCRIBER_ACTIVATION = 1;
    const TOKEN_TYPE_SUBSCRIBER_DEACTIVATION = 2;
    const TOKEN_TYPE_USER_REGISTRATION = 3;
    const TOKEN_TYPE_USER_PASSWORD_FORGET = 4;
    
    const STATUS_NEW = 0;
    const STATUS_USED = 1;
    const STATUS_EXPIRED = 2;
               
    public function generateToken()
    {
        $select = $this->select()->from(array('s' => 'subscribers'))
                       ->setIntegrityCheck(false)
                       ->where('s.status = ?',App_Model_Subscribers_DbTable::STATUS_ACTIVE);  
        $subscribers = $this->fetchAll($select);
        if($subscribers->count()){
            foreach($subscribers as $subscriber){
                $token = $this->fetchRow($this->select()->where('type_id = ?', $subscriber->id)->where('type =?', self::TOKEN_TYPE_SUBSCRIBER_DEACTIVATION)->where('status =?',self::STATUS_NEW));
                if(!$token){
                    $this->setNewsletterRemoveToken($subscriber->id);
                }
            }
        }
    }
    
    public function getToken()
    {   
        do {
            $key = md5(rand());
            $where =  "code = '{$key}'";
        } while($this->fetchRow($where));
        return $key;
    }
    
    public function setRegistrationToken($user_id)
    {
        $data = array(
            'type_id' => $user_id,
            'type' => self::TOKEN_TYPE_USER_REGISTRATION,
            'code' => $this->getToken(),
            'status' => self::STATUS_NEW,
            'created_at' => new Zend_Db_Expr('NOW()'),
            'key_expiration_date' => new Zend_Db_Expr('NOW() + INTERVAL 1 DAY'),
        );
        return parent::insert($data);
    }
    
    public function setNewsletterAddToken($subscriber_id)
    {
        $data = array(
            'type_id' => $subscriber_id,
            'type' => self::TOKEN_TYPE_SUBSCRIBER_ACTIVATION,
            'code' => $this->getToken(),
            'status' => self::STATUS_NEW,
            'created_at' => new Zend_Db_Expr('NOW()'),
            'key_expiration_date' => new Zend_Db_Expr('NOW() + INTERVAL 1 DAY'),
        );
        return parent::insert($data);
    } 
    
    public function setNewsletterRemoveToken($subscriber_id)
    {
        $data = array(
            'type_id' => $subscriber_id,
            'type' => self::TOKEN_TYPE_SUBSCRIBER_DEACTIVATION,
            'code' => $this->getToken(),
            'status' => self::STATUS_NEW,
            'created_at' => new Zend_Db_Expr('NOW()'),
            'key_expiration_date' => new Zend_Db_Expr('NOW() + INTERVAL 1 DAY'),
        );
        return parent::insert($data);
    }  
    public function setForgetPasswordToken($user)
    {
        $sql = 'DELETE FROM '.$this->_name.' WHERE type_id = "'.$user->id.'" 
                AND type = "'.self::TOKEN_TYPE_USER_PASSWORD_FORGET.'" AND status = "'.self::STATUS_NEW.'"';
        $this->getAdapter()->query($sql);
        $data = array(
            'type_id' => $user->id,
            'type' => self::TOKEN_TYPE_USER_PASSWORD_FORGET,
            'code' => $this->getToken(),
            'status' => self::STATUS_NEW,
            'created_at' => new Zend_Db_Expr('NOW()'),
            'key_expiration_date' => new Zend_Db_Expr('NOW() + INTERVAL 1 DAY'),
        );
        return parent::insert($data);
    }
    
    public function getByKey($key)
    {
        $select = $this->select()->where('code =?',$key)->where('status =?', self::STATUS_NEW);
        return $this->fetchRow($select);
    } 
    
    public function cleaningToken()
    {
        $select = $this->select()->from(array('t' => $this->_name),array('code'))
                       ->where('status =?',self::STATUS_USED)
                       ->orWhere('status = ?', self::STATUS_EXPIRED);
        $tokens = $this->fetchAll($select);
        if($tokens->count()> 0){
            foreach ($tokens as $token){
               $data[] = '"'.$token['code'].'"'; 
            }
            $array = implode($data,',');
            $sql = 'DELETE FROM '.$this->_name.' WHERE code IN ('.$array.')';
            $this->getAdapter()->query($sql);
        }
    }
    
    public function getActivationToken($user_id)
    {
        $select = $this->select()->where('type = "user_registration"')->where('type_id = ?', $user_id);
        if($this->fetchRow($select) == NULL){
            return $this->setRegistrationToken($user_id);  
        }
        return $this->fetchRow($select);
    }
}



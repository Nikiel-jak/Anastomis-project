<?php

class App_Model_Download_DbTable extends Orion_Model_DbTable
{  	
    protected $_name = 'download';
    protected $_primary = array('hash', 'email');
    
    const STATUS_TO_GENERATE = 0;
    const STATUS_GENERATED = 1;
    
    
    public function setQueue($zip, $email)
    {
        $data['data'] = serialize($zip);
        $data['email'] = $email;
        $data['status'] = self::STATUS_TO_GENERATE;
        $data['created_at'] = new Zend_Db_Expr('NOW()');
        $data['hash'] =  md5(time());
        $data['expired_at'] = NULL;
        return parent::insert($data);
    }
    
    public function getOneToGenerate()
    {
        $select = $this->select()->where('status = ?', self::STATUS_TO_GENERATE)->order('created_at ASC')->limit(1);
        return $this->fetchRow($select);
    }
    
    public function countToGenerate()
    {
        $select = $this->select()->from(array('d' => $this->_name),array(new Zend_Db_Expr('"value"') ,new Zend_Db_Expr('COUNT(d.hash)')))->where('status = ?', self::STATUS_TO_GENERATE)->order('created_at ASC');
        return $this->_db->fetchPairs($select);
    }
    
    public function getByHash($hash)
    {
        $select = $this->select()->where('hash = ?',$hash);
        return $this->fetchRow($select);
    }

}
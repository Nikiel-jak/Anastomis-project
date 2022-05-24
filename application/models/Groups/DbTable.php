<?php

class App_Model_Groups_DbTable extends Orion_Model_DbTable
{
    protected $_name = 'groups';
    protected $_primary  = array('id');
    
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    
    const DELETED_YES = 1;
    const DELETED_NO = 2;
    
    public function getAll($sql = null)
    {
        $select = $this->select()->where('deleted = ?',self::DELETED_NO);
        if($sql){
            return $select; 
        }
        return $this->fetchAll($select);
    }
    
    public function getActive($sql = null)
    {
        $select = $this->select()->where('status = ?', self::STATUS_ACTIVE)->where('deleted = ?',self::DELETED_NO);
        if($sql){
            return $select; 
        }
        return $this->fetchAll($select);
    }
    
    public function getActiveToForm()
    {
        $select = $this->getActive(true);
        $select->from(array('g' => $this->_name),array('id','name'));
        return $this->_db->fetchPairs($select);
    }
    
    public function create(array $data, $created_by = null, $language = true)
    {
        $data['deleted'] = self::DELETED_NO;
        return parent::create($data, $created_by, $language);
        
    }
    
    public function edit(array $data, $where, $modified_by = null)
    {
        $data['modified_by'] = Orion_Auth::getProfileId();
        $data['modified_at'] = new Zend_Db_Expr('NOW()');
        return parent::update($data, $where, $modified_by);
    }
    
    public static function getAvaliableStatus()
    {
        return array(
            self::STATUS_ACTIVE => 'aktywny',
            self::STATUS_INACTIVE => 'nieaktywny',
        );
    }

    public function setStatusActive($ids)
    {
        foreach($ids as $val){
            $where = 'id = '.$val;
            $up[] =$this->update(array('status' => self::STATUS_ACTIVE), $where);
        }
        return $up;
    }
    
    public function setStatusInactive($ids)
    {
        foreach($ids as $val){
            $where = 'id = '.$val;
            $up[] =$this->update(array('status' => self::STATUS_INACTIVE), $where);
        }
        return $up;
    }

    public function setDeleted($ids)
    {
        foreach($ids as $val){
            $where = 'id = '.$val;
            $up[] =$this->update(array('deleted' => self::DELETED_YES), $where);
        }
        return $up;
    }
    
}
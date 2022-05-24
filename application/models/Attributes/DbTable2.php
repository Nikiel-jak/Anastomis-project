<?php

class App_Model_Attributes_DbTable extends Orion_Model_DbTable
{
    protected $_name = 'attributes';
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
    
    public function create(array $data, $created_by = null, $language = true)
    {
        $data['deleted'] = self::DELETED_NO;
        return parent::create($data, $created_by, $language);
        
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
    
    public function getGroupWithAttributesToForm()
    {
        $select = $this->select()->from(array('a' => $this->_name))
                       ->setIntegrityCheck(false)
                      ->join(array('g' => 'groups'),'a.group_id = g.id',array('group_name' => 'name'))
                      ->where('g.status = ?',  App_Model_Groups_DbTable::STATUS_ACTIVE)
                      ->where('g.deleted = ?', App_Model_Groups_DbTable::DELETED_NO)
                      ->where('a.status = ?',  self::STATUS_ACTIVE)
                      ->where('a.deleted = ?', self::DELETED_NO);
        return $this->fetchAll($select);
    }
    
    public function getGroupWithAttributesToSite()
    {
        $select = $this->select()->from(array('a' => $this->_name))
                       ->setIntegrityCheck(false)
                      ->join(array('g' => 'groups'),'a.group_id = g.id',array('group_name' => 'name', 'group_id' => 'id','hint','can_hide','order'))
                      ->joinLeft(array('aa' => 'anastomosis_attributes'),'aa.attribute_id = a.id',array('MIN(CAST(aa.value AS SIGNED)) as min_value','MAX(CAST(aa.value AS SIGNED)) as max_value'))
                      ->where('g.status = ?',  App_Model_Groups_DbTable::STATUS_ACTIVE)
                      ->where('g.deleted = ?', App_Model_Groups_DbTable::DELETED_NO)
                      ->where('a.status = ?',  self::STATUS_ACTIVE)
                      ->group('attribute_id')
                      ->where('a.deleted = ?', self::DELETED_NO)
                      ->order(array('g.order ASC', 'a.id ASC'));
        return $this->fetchAll($select);
    }
}
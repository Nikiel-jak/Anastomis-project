<?php

class App_Model_Attributes_Values_DbTable extends Orion_Model_DbTable
{
    protected $_name = 'atributes_values';
    protected $_primary  = array('id');
    
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    
    const DELETED_YES = 1;
    const DELETED_NO = 2;
    
    
    public function getAllByAttribute($attribute_id, $sql = null)
    {
        $select = $this->select()->where('attribute_id = ?',$attribute_id)->where('deleted = ?',self::DELETED_NO);
        if($sql){
            return $select; 
        }
        return $this->fetchAll($select);
    }

    public function getActiveToFormByAttribute($attribute_id)
    {
        $select = $this->select()->from(array('av' => $this->_name),array('value','view'))->where('attribute_id = ?',$attribute_id)
                ->where('status = ?',self::STATUS_ACTIVE)
                ->where('deleted = ?',self::DELETED_NO);
        return $this->_db->fetchPairs($select);
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
    
}
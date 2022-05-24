<?php

class App_Model_Language_DbTable extends Orion_Model_DbTable
{   
    const LANG_DEFAULT = 1;
    
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;
    
    protected $_name = 'language';
   
    public static function getAvaliableStatus()
    {
        return array(
            self::STATUS_ACTIVE => 'aktywny',
            self::STATUS_INACTIVE => 'nieaktywny',
        );
    }
    
    public function getAvaliable($sql = false)
    {
        $select = $this->select()->where('status != ?',self::STATUS_DELETED);
        if($sql){
            return $select;
        }
        return $this->fetchAll($select);
    }
    
    public function getDefault()
    {
        $select = $this->select()->where('`default` = ? ',self::LANG_DEFAULT);
        return $this->fetchRow($select);
    }
    
    public function setStatusActive($ids)
    {
        if(is_array($ids)){
            $ids= implode(',', $ids);
        }
        $where = 'id IN ('.$ids.')';
        return $this->update(array('status' => self::STATUS_ACTIVE), $where);
    }
    
    public function setStatusInactive($ids)
    {
        if(is_array($ids)){
            $ids= implode(',', $ids);
        }
        $where = 'id IN ('.$ids.')';
        return $this->update(array('status' => self::STATUS_INACTIVE), $where);
    }

    public function setDeleted($ids)
    {
        if(is_array($ids)){
            $ids= implode(',', $ids);
        }
        $where = 'id IN('.$ids.')';
        return $this->update(array('status' => self::STATUS_DELETED), $where);
    }
    
    public function addLanguage($data)
    {
        if($data['default'] == 1){
            $where = $this->getAdapter()->quoteInto('id > ?', 0);
            parent::update(array('default' => 2), $where);
        }
        mkdir(APPLICATION_PATH.'/languages/'.$data['prefix'].'/site/' ,777, true);
        mkdir(APPLICATION_PATH.'/languages/'.$data['prefix'].'/admin/' ,777, true);
        mkdir(APPLICATION_PATH.'/languages/'.$data['prefix'].'/common/' ,777, true);
        $data['created_by'] = Orion_Auth::getProfileId();
        $data['created_at'] = new Zend_Db_Expr('NOW()');
        $data['modified_by'] = Orion_Auth::getProfileId();
        $data['modified_at'] = new Zend_Db_Expr('NOW()');
        return parent::insert($data);
    }
    
    public function updateLanguage($data, $language)
    {
        if($data['default'] == 1){
            $where = $this->getAdapter()->quoteInto('id > ?', 0);
            parent::update(array('default' => 2), $where);
        }
        if($language->prefix != $data['prefix']){
            rename(APPLICATION_PATH.'/languages/'.$language->prefix, APPLICATION_PATH.'/language/old_'.$language->prefix);
        }
        mkdir(APPLICATION_PATH.'/languages/'.$data['prefix'].'/site/' ,777, true);
        mkdir(APPLICATION_PATH.'/languages/'.$data['prefix'].'/admin/' ,777, true);
        mkdir(APPLICATION_PATH.'/languages/'.$data['prefix'].'/common/' ,777, true);
        $data['modified_by'] = Orion_Auth::getProfileId();
        $data['modified_at'] = new Zend_Db_Expr('NOW()');
        $where = $this->getAdapter()->quoteInto('id = ?', $language->id);
        return parent::update($data, $where);
    }
    
    public function getByPrefix($prefix)
    {
        $select= $this->select()->where('prefix = ?',$prefix);
        return $this->fetchRow($select);
    }
}
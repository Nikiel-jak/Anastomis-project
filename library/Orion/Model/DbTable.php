<?php

class Orion_Model_DbTable extends Zend_Db_Table_Abstract
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    
    public function findById($id)
    {
        if ($id === null) return null;
        return $this->find($id)->current();
    }

    public function getById($id)
    {
        $record = $this->findById($id);
        if (!$record) throw new Zend_Db_Table_Exception('Record does not exist');

        return $record;
    }
    
    public function getCols()
    {
        return $this->_cols;
    }
    
    public function getMetaData()
    {
        return $this->_metadata;
    }
    
    public function getAll($sql = null)
    {
        $select = $this->select();
        if($sql){
            return $select; 
        }
        return $this->fetchAll($select);
    }
    
    public static function getAvaliableStatus()
    {
        return array(
            self::STATUS_ACTIVE => 'aktywny',
            self::STATUS_INACTIVE => 'nieaktywny',
        );
    }
    
        /**
     * Metoda dodaje do danego wiersz do bazy
     * Uzupełnia automatycznie dane o osobie dodającej, modyfikującej(opcjonalnie)
     * Uzupełnia automatycznie dane o dacie dodania, modyfikacji(opcjonalnie)
     * 
     * @param   array       $data       dane wiersza
     * @param   null|int    $created_by User Id
     * @return  int         The id row inserted
     */
    public function create(array $data, $created_by = null, $language = true)
    {
        $data['created_by'] = ($created_by == null) ? Orion_Auth::getProfileId() : $created_by;
        $data['created_at'] = new Zend_Db_Expr('NOW()');

        $data['modified_by'] = ($created_by == null) ? Orion_Auth::getProfileId() : $created_by;
        $data['modified_at'] = new Zend_Db_Expr('NOW()');
        
        if($language === true) {
            $data['language_id'] = Orion_Language::getContentId();
        }elseif($language == true) {
            $data['language_id'] = (int)$language;
        }
        return $this->insert($data);
    }
    
    /**
     * Metoda edytuje wiersz w bazie
     * Uzupełnia automatycznie dane o osobie modyfikującej
     * Uzupełnia automatycznie dane o dacie modyfikacji
     *
     * @param  array        $data  Column-value pairs.
     * @param  array|string $where An SQL WHERE clause, or an array of SQL WHERE clauses.
     * @return int          The number of rows updated.
     */
    public function edit(array $data, $where, $modified_by = null)
    {
        $data['modified_by'] = ($modified_by == null) ? Orion_Auth::getProfileId() : $modified_by;
        $data['modified_at'] = new Zend_Db_Expr('NOW()');
        
        return $this->update($data, $where);
    }
    
    /**
     * Metoda ustawia wszystkim wybranym elementon status aktywnych
     * 
     * @param int|array     $ids id or ids element
     * @return int          The number of rows updated.
     */
    public function batchActionActive($ids, $where = null)
    {
        $data = array('status' => self::STATUS_ACTIVE);

        if(is_array($ids)){
            $ids= implode(',', $ids);
        }
        
        if($where == null) {
            $where = array();
        }
        
        $where[] = 'id IN (' . $ids . ')';
        
        return $this->edit($data, $where);
    }
    
    /**
     * Metoda ustawia wszystkim wybranym elementon status nieaktywny
     * 
     * @param int|array     $ids id or ids element
     * @return int          The number of rows updated.
     */
    public function batchActionInActive($ids, $where = null)
    {
        
        $data = array('status' => self::STATUS_INACTIVE);
        
        if(is_array($ids)){
            $ids= implode(',', $ids);
        }
        
        if($where == null) {
            $where = array();
        }
        
        $where[] = 'id IN (' . $ids . ')';
        
        return $this->edit($data, $where);
    }
    
    /**
     * Metoda ustawia wszystkim wybranym elementon status usunięty
     * 
     * @param int|array     $ids id or ids element
     * @return int          The number of rows updated.
     */
    public function batchActionDelete($ids, $where = null)
    {
        
        $data = array('deleted' => self::DELETED_YES);
        
        if(is_array($ids)){
            $ids= implode(',', $ids);
        }

        if($where == null) {
            $where = array();
        }
        
        $where[] = 'id IN (' . $ids . ')';
        
        return $this->edit($data, $where);
    }
}

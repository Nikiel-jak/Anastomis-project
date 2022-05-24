<?php
class Orion_Validate_ValueNotExist extends Zend_Validate_Abstract
{
    const VALUE_EXIST = 'valueExist';

    protected $_fieldName;
    protected $_tableName;

    protected $_messageTemplates = array(
        self::VALUE_EXIST => 'This value is exist.'
    );

    public function __construct($tableName = 'users', $fieldName = 'login')
    {
        $this->setFieldName($fieldName);
        $this->setTableName($tableName);
    }

    public function setFieldName($fieldName)
    {
        if (!is_string($fieldName)) {
            throw new Zend_Exception('FieldName must be a string');
        }
        $this->_fieldName = $fieldName;
    }

    public function setTableName($tableName)
    {
        if (!is_string($tableName)) {
            throw new Zend_Exception('TableName must be a string');
        }
        $this->_tableName = $tableName;
    }

    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);

        $db = Zend_Db_Table::getDefaultAdapter();
        $result = $db->fetchOne($db->quoteInto(
            "SELECT {$this->_fieldName} FROM {$this->_tableName} WHERE  {$this->_fieldName} = ?",
            $value
        ));
        if ($result == false) {
			return true;
        }

        $this->_error(self::VALUE_EXIST);
        return false;
    }
}

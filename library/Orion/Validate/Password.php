<?php
class Orion_Validate_Password extends Zend_Validate_Abstract
{
    const NOT_MATCH = 'notMatch';

    protected $_fieldName;

    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Password confirmation does not match'
    );

    public function __construct($fieldName = 'password')
    {
        $this->setFieldName($fieldName);
    }

    public function setFieldName($fieldName)
    {
        if (!is_string($fieldName)) {
            throw new Zend_Exception('FieldName must be a string');
        }
        $this->_fieldName = $fieldName;
    }

    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);

        if (is_array($context)) {
            if (isset($context[$this->_fieldName])
                && ($value == $context[$this->_fieldName]))
            {
                return true;
            }
        } elseif (is_string($context) && ($value == $context)) {
            return true;
        }

        $this->_error(self::NOT_MATCH);
        return false;
    }
}

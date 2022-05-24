<?php
class Orion_Validate_CheckboxRequired extends Zend_Validate_Abstract
{
    const NOT_CHECKED = 'checkboxRequiredNotChecked';

    protected $_messageTemplates = array(
        self::NOT_CHECKED => 'To pole jest wymagane'
    );

    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);

        if ($value != 1) {
            $this->_error(self::NOT_CHECKED);
            return false;
        }

        return true;
    }
}

<?php
class Orion_Validate_Old extends Zend_Validate_Abstract
{

    protected $_fieldName;
    protected $_tableName;
    protected $_minimum_old;
    protected $_fieldsName;

    const YOU_ARE_TOO_YOUNG ='tooYoung';
    
    protected $_messageTemplates = array(
        self::YOU_ARE_TOO_YOUNG => 'Musisz mieć skończone 13 lat'
    );


    /**
     * Example
     * new Yeti_Validate_Old(13, array('year'=>'birth_date_year','month'=>'birth_date_month','day'=>'birth_date_day'))
     * 
     *
     */
    public function __construct($minimum, $fieldsName)
    {
        $this->_minimum_old = $minimum;
        $this->_fieldsName = $fieldsName;
    }

    public function isValid($value, $context = null)
    {
        
        $wiek = date('Y') - $context[$this->_fieldsName['year']];
        if ((date('m') < $context[$this->_fieldsName['month']]) || (date('m') == $context[$this->_fieldsName['month']] && date('d') < $context[$this->_fieldsName['day']])) {
            $wiek--;
        }
       
        if($wiek>=$this->_minimum_old){
            return true;
        }else{
            $this->_error(self::YOU_ARE_TOO_YOUNG);
            return false;
        }
    }
}

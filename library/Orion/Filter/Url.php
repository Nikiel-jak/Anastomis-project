<?php

class Orion_Filter_Url implements Zend_Filter_Interface
{
    protected $cyrillic = array(
        '?'=>'A', '?'=>'a',
        '?'=>'B', '?'=>'b',
        '?'=>'V', '?'=>'v',
        '?'=>'G', '?'=>'g',
        '?'=>'D', '?'=>'d',
        '?'=>'E', '?'=>'e',
        '?'=>'YO', '?'=>'yo',
        '?'=>'ZH', '?'=>'zh',
        '?'=>'Z', '?'=>'z',
        '?'=>'I', '?'=>'i',
        '?'=>'Y', '?'=>'y',
        '?'=>'K', '?'=>'k',
        '?'=>'L', '?'=>'l',
        '?'=>'M', '?'=>'m',
        '?'=>'N', '?'=>'n',
        '?'=>'O', '?'=>'o',
        '?'=>'P', '?'=>'p',
        '?'=>'R', '?'=>'r',
        '?'=>'S', '?'=>'s',
        '?'=>'T', '?'=>'t',
        '?'=>'U', '?'=>'u',
        '?'=>'F', '?'=>'f',
        '?'=>'KH', '?'=>'kh',
        '?'=>'TS', '?'=>'ts',
        '?'=>'CH', '?'=>'ch',
        '?'=>'SH', '?'=>'sh',
        '?'=>'SHCH', '?'=>'shch',
        '?'=>'', '?'=>'',
        '?'=>'Y', '?'=>'y',
        '?'=>'', '?'=>'',
        '?'=>'E', '?'=>'e',
        '?'=>'YU', '?'=>'yu',
        '?'=>'YA', '?'=>'ya',
    );

    protected $polish = array(
        'Ą' => 'A', 'ą' => 'a',
        'Ć' => 'C', 'ć' => 'c',
        'Ę' => 'E', 'ę' => 'e',
        'Ł' => 'L', 'ł' => 'l',
        'Ó' => 'O', 'ó' => 'o',
        'Ś' => 'S', 'ś' => 's',
        'Ż' => 'Z', 'ż' => 'z',
        'Ź' => 'Z', 'ź' => 'z',
    );

    public function filter($value)
    {
		$value = str_replace(array_keys($this->cyrillic),array_values($this->cyrillic),$value);
		$value = str_replace(array_keys($this->polish),array_values($this->polish),$value);
		$value = str_replace(array('\'','~','`','"','^'),'',iconv('UTF-8','ASCII//TRANSLIT',$value));
		$filterChain = new Zend_Filter();
		$filterChain->addFilter(new Zend_Filter_PregReplace('/[^a-zA-Z0-9\-]/','-'))
			->addFilter(new Zend_Filter_StringToLower())
			->addFilter(new Zend_Filter_PregReplace('/[\-]{2,}/','-'));
		return trim($filterChain->filter($value),'-');
    }
}

<?php

class View_Helper_Snippet extends Zend_View_Helper_Abstract
{
    /**
     * Metoda ucina tekst na zadanej pozycji $length,rezultat kończąc podanym $tail
     * @param string $text
     * @param int $length
     * @param string $tail
     * @return string
     */
    public function snippet($text, $length = 80, $tail="...")
    {
        $text = strip_tags(html_entity_decode($text,ENT_COMPAT,'UTF-8'));

        $txtl = mb_strlen($text);
        if($txtl > $length) {
            for($i=1;$text[$length-$i]!=" ";$i++) {
                if($i == $length) {
                    return mb_substr($text,0,$length) . $tail;
                }
            }
            $text = mb_substr($text,0,$length-$i) . $tail;
        }
        return $text;
    }
}
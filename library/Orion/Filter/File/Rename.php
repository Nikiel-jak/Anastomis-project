<?php

class Orion_Filter_File_Rename extends Zend_Filter_File_Rename
{
    /**
     * Returns only the new filename without moving it
     * But existing files will be erased when the overwrite option is true
     *
     * @param  string  $value  Full path of file to change
     * @param  boolean $source Return internal informations
     * @return string The new filename which has been set
     */
    public function getNewName($value, $source = false)
    {
        $file = $this->_getFileName($value);
        $pathinfo = pathinfo($file['target']);

        $pathinfo['filename'] = preg_replace('/[^a-zA-Z0-9]/', '_', $pathinfo['filename']);

        if(!isset($pathinfo['extension']))
        {
            $pathinfo['extension'] = '';
        }
        else
        {
            $pathinfo['extension'] = '.' . $pathinfo['extension'];
        }

        $file['target'] = $pathinfo['dirname'] . DIRECTORY_SEPARATOR . $pathinfo['filename'] . $pathinfo['extension'];

        if (($file['overwrite'] == true) && (file_exists($file['target']))) {
            unlink($file['target']);
        }
        if (file_exists($file['target'])) {
            $i = 1;
            do {
                $i++;
                $file['target'] = $pathinfo['dirname'] . DIRECTORY_SEPARATOR . $pathinfo['filename'] . '_' . $i . $pathinfo['extension'];
            } while (file_exists($file['target']));
        }
        if ($source) {
            return $file;
        }
        return $file['target'];
    }
    

}

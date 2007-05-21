<?php

class Varien_Simplexml_Config_Cache_Abstract extends Varien_Object
{
    public function __construct($data=array())
    {
        $this->setComponents(array());
        $this->setIsAllowedToSave(true);
        
        parent::__construct($data);
    }
    
    public function validateComponents(array $data)
    {
        // check that no source files were changed or check file exsists
        foreach ($data as $sourceFile=>$mtime) {
            if (!is_file($sourceFile) || filemtime($sourceFile)!==$mtime) {
                return false;
            }
        }
        return true;
    }
}
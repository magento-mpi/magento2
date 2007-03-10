<?php

class Mage_Core_Config 
{
    static private $_config = null;
    
    static function getConfig()
    {
        return self::$_config;
    }
    
    static function loadFile($fileName, $moduleName='')
    {
        if (''===$moduleName) {
            $root = Mage::getRoot('etc');
        } else {
            $root = Mage::getModuleInfo($moduleName)->getRoot('etc');
        }
        
        $xml = simplexml_load_file($root.DS.$fileName);
        
        self::append($xml);
    }
    
    static function append($source, $target=null)
    {
        if (is_null(self::$_config)) {
            self::$_config = $source;
            return true;
        }
        
        if (is_null($target)) {
            $target = self::$_config;
        }
        
        foreach ($source as $key=>$node) {
            if (!isset($target->$key)) {
                $target->addChild($key, $node);
            } else {
                self::append($node, $target->$key);
            }
        }
        
    }
    
}
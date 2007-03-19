<?php

/**
 * Block factory
 * 
 * For block generation you must define Data source class, data source class method,
 * parameters array and block template
 *
 * @copyright  Varien, 2007
 * @version    1.0 
 * @author     Moshe Gurvich <moshe@varien.com>
 * @author     Soroka Dmitriy <dmitriy@varien.com>
 */

class Mage_Core_Block
{
    
    
    /**
     * Blocks registry
     *
     * @var array
     */
    private static $_blocks = array();
    
    /**
     * Cache of block callbacks to output during rendering
     *
     * @var array
     */
    private static $_output = array();
    
    /**
     * Save block in blocks registry
     *
     * @param string $name
     * @param Mage_Core_Block_Abstract $block
     */
    public static function setBlock($name, $block)
    {
        self::$_blocks[$name] = $block;
    }
    
    /**
     * Remove block from registry
     *
     * @param string $name
     */
    public static function unsetBlock($name)
    {
        self::$_blocks[$name] = null;
        unset(self::$_blocks[$name]);
    }
    
    public static function getType($type='')
    {
        $types = Mage::getConfig('/')->global->blockTypes;
        if (''===$type) {
            return $types;
        } else {
            return $types->$type;
        }
    }
    
    /**
     * Block Factory
     * 
     * @param     string $type
     * @param     string $blockName
     * @param     array $attributes
     * @return    Mage_Core_Block_Abtract
     * @author    Moshe Gurvich <moshe@varien.com>
     * @author    Soroka Dmitriy <dmitriy@varien.com>
     */
    public static function createBlock($type, $name='', array $attributes = array())
    {
        #Mage::setTimer(__METHOD__);

        if (!$className = (string)self::getType($type)->class) {
            Mage::exception('Invalid block type ' . $type);
        }

        $block = new $className();
        
        if (empty($name) || '.'===$name{0}) {
            $block->setInfo('anonymous', true);
            if (!empty($name)) {
                $block->setInfo('anonSuffix', substr($name, 1));
            }
            $name = 'ANONYMOUS_'.sizeof(self::$_blocks);
        }
        
        $block->setInfo(array('type'=>$type, 'name'=>$name));
        $block->setAttribute($attributes);
        
        self::$_blocks[$name] = $block;
        
        #Mage::setTimer(__METHOD__, true);

        return self::$_blocks[$name];
    }
    
    public static function getAllBlocks()
    {
        return self::$_blocks;
    }
    
    public static function getBlockByName($name)
    {
        if (isset(self::$_blocks[$name])) {
            return self::$_blocks[$name];
        } else {
            return false;
        }
    }
    
    public static function getBlocksByGroup($groupName)
    {
        $blocks = array();
        foreach (self::$_blocks as $name=>$block) {
            if ($block->getInfo('groupName')==$groupName) {
                $blocks[$name] = $block;
            }
        }
        return $blocks;
    }
    
    public static function getOutputBlocks()
    {
        return self::$_output;
    }    
    
    /**
     * Parse and run block script array
     * 
     */
    static public function loadArray($arr, $block=null)
    {
        if (!is_array($arr) || empty($arr[0]) || !is_string($arr[0])) {
            return $arr;
        }
       
        $type = $arr[0]{0};
        $entity = substr($arr[0], 1);
        array_shift($arr);
        
        switch ($type) {
            
            case ':': 
                // declare layoutUpdate name and check for dependancies
                $config = array_shift($arr);
                if (!empty($config) && is_array($config)) {
                    foreach ($config as $key=>$value) {
                        switch ($key) {
                            case "output":
                                foreach ($value as $callback) {
                                    $blockName = $callback[0];
                                    $method = isset($callback[1]) ? $callback[1] : 'toString';
                                    self::$_output[] = array($blockName, $method);
                                }
                                break;
                        }
                    }
                }
                
                $blocks = array();
                foreach ($arr as $block) {
                    $blocks[] = self::loadArray($block);
                }
                $result = $blocks;  
                break;    
                                
            case '+': 
            case '#': 
                if ('+'===$type) {
                    // create new block using this entity as block type
                    if (!empty($arr[0]) && is_string($arr[0]) && ('#'===$arr[0]{0})) {
                        $name = substr($arr[0], 1);
                        array_shift($arr);
                    } else {
                        $name = '';
                    }
                    $block = self::createBlock($entity, $name);
                } else {
                    // retrieve existing block by entity as name
                    $block = self::getBlockByName($entity);
                }
                
                if (empty($block)) {
                    $result = false;
                    break;
                }
                foreach ($arr as $action) {
                    self::loadArray($action, $block);
                }
                $result = $block;
                break;
                
            case '.': 
                // run action method for $block
                if (empty($block)) {
                    $result = false;
                    break;
                }
                $args = array();
                foreach ($arr as $arg) {
                    $args[] = self::loadArray($arg);
                }
                $result = call_user_func_array(array($block, $entity), $args);
                break;
        }
        
        return $result;
    }

    static public function loadJsonFile($fileName, $moduleName='')
    {
        #Varien_Profiler::setTimer('loadJson');
        
        $baseUrl = Mage::getBaseUrl();
        $baseSkinUrl = Mage::getBaseUrl('skin');
        if (''!==$moduleName) {
           $baseModuleUrl = Mage::getBaseUrl('', $moduleName);
        } else {
            $baseModuleUrl = '';
        }
        $baseJsUrl = Mage::getBaseUrl('js');
        
        if ('/'!==$fileName{0}) {
            $fileName = Mage::getBaseDir('layout').DS.$fileName;
        }
        
        $json = file_get_contents($fileName, true);
        eval('$json = "'.addslashes($json).'";');
        
        $arr = Zend_Json::decode($json);
        #echo "TEST:"; print_r($arr);

        #Varien_Profiler::setTimer('loadJson', true);

        #Varien_Profiler::setTimer('loadArray');
        self::loadArray($arr);
        #Varien_Profiler::setTimer('loadArray', true);
    }

}// Class Mage_Home_ContentBlock END
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
        $types = Mage::getConfig()->getXml()->global->blockTypes;
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
    
    public static function addBlock($className, $blockName)
    {
        $block = new $className();
        $block->setInfo(array('name'=>$blockName));
        self::$_blocks[$blockName] = $block;
        return $block;
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
    
    public static function addOutputBlock($blockName, $method='toString')
    {
        self::$_output[] = array($blockName, $method);
    }
    
    public static function getOutputBlocks()
    {
        return self::$_output;
    }    
}// Class Mage_Home_ContentBlock END
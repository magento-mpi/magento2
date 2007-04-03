<?php

/**
 * Layout blocks registry and factory
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
    protected $_blocks = array();
    
    /**
     * Cache of block callbacks to output during rendering
     *
     * @var array
     */
    protected $_output = array();
    
    /**
     * Save block in blocks registry
     *
     * @param string $name
     * @param Mage_Core_Block_Abstract $block
     */
    public function setBlock($name, $block)
    {
        $this->_blocks[$name] = $block;
    }
    
    /**
     * Remove block from registry
     *
     * @param string $name
     */
    public function unsetBlock($name)
    {
        $this->_blocks[$name] = null;
        unset($this->_blocks[$name]);
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
    public function createBlock($type, $name='', array $attributes = array())
    {
        #Mage::setTimer(__METHOD__);

        if (!$className = (string)Mage::getConfig()->getBlockType($type)->class) {
            Mage::exception('Invalid block type ' . $type);
        }

        $block = new $className();
        
        if (empty($name) || '.'===$name{0}) {
            $block->setInfo('anonymous', true);
            if (!empty($name)) {
                $block->setInfo('anonSuffix', substr($name, 1));
            }
            $name = 'ANONYMOUS_'.sizeof($this->_blocks);
        }
        
        $block->setInfo(array('type'=>$type, 'name'=>$name));
        $block->setAttribute($attributes);
        
        $this->_blocks[$name] = $block;
        
        #Mage::setTimer(__METHOD__, true);

        return $this->_blocks[$name];
    }
    
    /**
     * Add a block to registry, create new object if needed
     *
     * @param string|Mage_Core_Block_Abstract $blockClass
     * @param string $blockName
     * @return Mage_Core_Block_Abstract
     */
    public function addBlock($blockClass, $blockName)
    {
        if (is_string($blockClass)) {
            $block = new $blockClass();
        } else {
            $block = $blockClass;
        }
        $block->setInfo(array('name'=>$blockName));
        $this->_blocks[$blockName] = $block;
        return $block;
    }
    
    /**
     * Retrieve all blocks from registry as array
     *
     * @return array
     */
    public function getAllBlocks()
    {
        return $this->_blocks;
    }
    
    /**
     * Get block object by name
     *
     * @param string $name
     * @return Mage_Core_Block_Abstract
     */
    public function getBlockByName($name)
    {
        if (isset($this->_blocks[$name])) {
            return $this->_blocks[$name];
        } else {
            return false;
        }
    }
    
    /**
     * Add a block to output
     *
     * @param string $blockName
     * @param string $method
     */
    public function addOutputBlock($blockName, $method='toString')
    {
        $this->_output[] = array($blockName, $method);
    }
    
    /**
     * Get all blocks marked for output
     *
     * @return array
     */
    public function getOutputBlocks()
    {
        return $this->_output;
    }    
}// Class Mage_Home_ContentBlock END
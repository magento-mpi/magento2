<?php

/**
 * Layout configuration class
 *
 */
class Mage_Core_Model_Layout extends Varien_Simplexml_Config
{
    /**
     * Layout arguments substitution
     *
     * @var array
     */
    protected $_subst = array('keys'=>array(), 'values'=>array());
        
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
     * Initialize layout configuration for $id key
     *
     * @param string $id
     */
    public function init($id)
    {
        $this->_elementClass = Mage::getConfig()->getModelClassName('core', 'layout_element');

        $this->setCacheDir(Mage::getBaseDir('var').DS.'cache'.DS.'layout');
        $this->setCacheKey($id);
        
        if ($xml = $this->loadCache()) {
            $this->setXml($xml);
        } else {
            $this->setXml('<layout/>');
        }
        return $this;
    }
    
    public function setSubst($subst)
    {
        foreach ($subst as $k=>$v) {
            $this->_subst['keys'][] = '{{'.$k.'}}';
            $this->_subst['values'][] = $v;
        }
        return $this;
    }
    
    /**
     * Load layout configuration update from file
     *
     * @param Varien_Simplexml_Element $args
     */
    public function loadUpdate($args)
    {
        $fileName = (string)$args->file;
        $moduleName = (string)$args->module;
        $fileName = Mage::getBaseDir('layout').DS.$fileName;
        $this->addCacheStat($fileName);
        
        $update = $this->loadFile($fileName);
        
        $update->prepare($args);
        foreach ($update as $child) {
            $this->getXml()->appendChild($child);
        }
        return $this;
    }
 
    /**
     * Load all updates from main config for the $area and $id
     *
     * @param string $area
     * @param string $id
     * @return boolean
     */
    public function loadUpdatesFromConfig($area, $id)
    {
        $layoutConfig = Mage::getConfig()->getXml("$area/layouts/$id");
        if (!empty($layoutConfig)) {
            $updates = $layoutConfig->updates->children();
            foreach ($updates as $update) {
                $this->loadUpdate($update);
            }
        }
        return $this;
    }
    
    protected function _processFileData($data)
    {
        $substKeys = array();
        $substValues = array();
        $subst = Mage::getConfig()->getPathVars();
        foreach ($subst as $k=>$v) {
            $substKeys[] = '{{'.$k.'}}';
            $substValues[] = $v;
        }
        return str_replace($substKeys, $substValues, $data);
    }
    
    /**
     * Create layout blocks from configuration
     *
     * @param Mage_Core_Layout_Element|null $parent
     */
    public function generateBlocks($parent=null)
    {
        if (empty($parent)) {
            $parent = $this->getXml();
        }
        foreach ($parent as $node) {
            switch ($node->getName()) {
                case 'block':
                    $className = (string)$node['class'];
                    $blockName = (string)$node['name'];
                    $block = $this->addBlock($className, $blockName);
                    
                    if (!empty($node['parent'])) {
                        $parentName = (string)$node['parent'];
                        $parent = $this->getBlock($parentName);
                        
                        if (isset($node['as'])) {
                            $as = (string)$node['as'];
                            $parent->setChild($as, $block);
                        } elseif (isset($node['before'])) {
                            $sibling = (string)$node['before'];
                            if ('-'===$sibling) {
                                $sibling = '';
                            }
                            $parent->insert($block, $sibling);
                        } elseif (isset($node['after'])) {
                            $sibling = (string)$node['after'];
                            if ('-'===$sibling) {
                                $sibling = '';
                            }
                            $parent->insert($block, $sibling, true);
                        } else {
                            $parent->append($block);
                        }
                    }
                    if (!empty($node['output'])) {
                        $method = (string)$node['output'];
                        $this->addOutputBlock($blockName, $method);
                    }
                    $this->generateBlocks($node);
                    break;
                    
                case 'reference':
                    $this->generateBlocks($node);
                    break;

                case 'action':
                    $name = (string)$node['block'];
                    $block = $this->getBlock($name);
                    $method = (string)$node['method'];
                    $args = (array)$node->children();
                    unset($args['@attributes']);
                    if (isset($node['json'])) {
                        $json = explode(' ', (string)$node['json']);
                        foreach ($json as $arg) {
                            $args[$arg] = Zend_Json::decode($args[$arg]);
                        }
                    }
#echo "<hr><pre>".$name."::".$method." / "; print_r($args); echo "</pre>";
                    call_user_func_array(array($block, $method), $args);
                    break;
            }
        }
    }
    
    
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

        if (!$className = Mage::getConfig()->getBlockTypeConfig($type)->getClassName()) {
            Mage::exception('Invalid block type ' . $type);
        }

        $block = new $className();
        
        if (empty($name) || '.'===$name{0}) {
            $block->setIsAnonymous(true);
            if (!empty($name)) {
                $block->setAnonSuffix(substr($name, 1));
            }
            $name = 'ANONYMOUS_'.sizeof($this->_blocks);
        }
        
        $block->setType($type)->setName($name)->setLayout($this);
        $block->addData($attributes);
        
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
    public function addBlock($block, $blockName)
    {
        if (is_string($block)) {
            $blockObj = new $block();
        } else {
            $blockObj = $block;
        }
            
        $blockObj->setName($blockName)->setLayout($this);
        $this->_blocks[$blockName] = $blockObj;
        return $blockObj;
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
    public function getBlock($name)
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
    public function addOutputBlock($blockName, $method='toHtml')
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
}
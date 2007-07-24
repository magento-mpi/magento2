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
     * Blocks cache
     *
     * @var Zend_Cache_Core
     */
    protected $_blockCache = null;
    
    /**
     * Cache of block callbacks to output during rendering
     *
     * @var array
     */
    protected $_output = array();
    
    protected $_area;
    
    public function __construct($data=array())
    {
        $this->_elementClass = Mage::getConfig()->getModelClassName('core/layout_element');
        parent::__construct($data);
    }
    
    public function setArea($area)
    {
    	$this->_area = $area;
    	return $this;
    }
    
    public function getArea()
    {
    	return $this->_area;
    }
    
    public function getCache()
    {
        if (!$this->_cache) {
            $this->_cache = Zend_Cache::factory('Core', 'File', array(), array(
                'cache_dir'=>Mage::getBaseDir('cache_layout')
            ));
        }
        return $this->_cache;
    }
    
    public function setBlockCache($frontend='Core', $backend='File', 
    	array $frontendOptions=array(), array $backendOptions=array())
    {
        if (empty($frontendOptions['lifetime'])) {
            $frontendOptions['lifetime'] = 7200;
        }
        if (empty($backendOptions['cache_dir'])) {
            $backendOptions['cache_dir'] = Mage::getBaseDir('cache_block');
        }
        $this->_blockCache = Zend_Cache::factory($frontend, $backend, $frontendOptions, $backendOptions);
        return $this;
    }
    
    public function getBlockCache()
    {
        if (empty($this->_blockCache)) {
            $this->setBlockCache();
        }
        return $this->_blockCache;
    }
    
    /**
     * Initialize layout configuration for $id key
     *
     * @param string $id
     */
    public function init($id)
    {
        if (Mage::getSingleton('core/store')->getCode()) {
            $id = Mage::getSingleton('core/store')->getCode() . '_' . $id;
        }
        $this->setCacheId($id);
        if (!$this->loadCache()) {
            $this->loadString('<layout/>');
            $this->setCacheChecksum('');
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
     * @param string $fileName
     */
    public function loadUpdateFile($fileName)
    {
        $mergeLayout = Mage::getModel('core/layout');
        $mergeLayout->loadFile($fileName);
        if ($mergeLayout) {
        	$this->updateCacheChecksum(filemtime($fileName));
        	$this->mergeUpdate($mergeLayout->getNode());
        }

        return $this;
    }
    
    public function mergeUpdate($update)
    {
        #$update->prepare($args);
        foreach ($update->children() as $child) {
            if ($child->getName()==='remove') {
                if (isset($child['method'])) {
                    $this->removeAction((string)$child['name'], (string)$child['method']);
                } else {
                    $this->removeBlock((string)$child['name']);
                }
            } else {
                $this->getNode()->appendChild($child);
            }
        }
        return $this;
    }
    
    public function removeBlock($blockName, $parent=null)
    {
        if (is_null($parent)) {
            $parent = $this->getNode();
        }
        foreach ($parent->children() as $children) {
echo "TEST:".$children[0];
            for ($i=0, $l=sizeof($children); $i<$l; $i++) {
                $child = $children[$i];
                if ($child->getName()==='block' && $blockName===(string)$child['name']) {
                    unset($parent->block[$i]);
                }
                $this->removeBlock($blockName, $child);
            }
        }
        return $this;
    }
    
    public function removeAction($blockName, $method, $parent=null)
    {
        if (is_null($parent)) {
            $parent = $this->getNode();
        }
        foreach ($parent->children() as $children) {
            for ($i=0, $l=sizeof($children); $i<$l; $i++) {
                $child = $children[$i];
                if ($child->getName()==='action' && $blockName===(string)$child['name'] && $method===(string)$child['method']) {
echo "TEST:".$i;
                    unset($parent->action[$i]);
                }
                $this->removeAction($blockName, $method, $child);
            }
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
        $updates = Mage::getConfig()->getNode("$area/layouts/$id/updates");
        if (!empty($updates)) {
            foreach ($updates->children() as $update) {
                $fileName = Mage::getDesign()->getLayoutFilename((string)$update->file);
                $this->loadUpdateFile($fileName);
            }
        }
        return $this;
    }
    
    public function processFileData($data)
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
            $parent = $this->getNode();
        }
        foreach ($parent as $node) {
            switch ($node->getName()) {
                case 'block':
                    $this->_generateBlock($node, $parent);
                    $this->generateBlocks($node);
                    break;
                    
                case 'reference':
                    $this->generateBlocks($node);
                    break;

                case 'action':
                    $this->_generateAction($node, $parent);
                    break;
            }
        }
    }
    
    protected function _generateBlock($node, $parent)
    {
        if (!empty($node['class'])) {
            $className = (string)$node['class'];
        } else {
            $className = Mage::getConfig()->getBlockClassName((string)$node['type']);
        }
        $blockName = (string)$node['name'];
        
        $_profilerKey = 'BLOCK: '.$blockName;
        Varien_Profiler::start($_profilerKey);
        
        $block = $this->addBlock($className, $blockName);
        
        if (!empty($node['parent'])) {
            $parentBlock = $this->getBlock((string)$node['parent']);
        } else {
            $parentName = $parent->getBlockName();
            if (!empty($parentName)) {
                $parentBlock = $this->getBlock($parentName);
            }
        }
        if (!empty($parentBlock)) {   
            if (isset($node['as'])) {
                $as = (string)$node['as'];
                $parentBlock->setChild($as, $block);
            } elseif (isset($node['before'])) {
                $sibling = (string)$node['before'];
                if ('-'===$sibling) {
                    $sibling = '';
                }
                $parentBlock->insert($block, $sibling);
            } elseif (isset($node['after'])) {
                $sibling = (string)$node['after'];
                if ('-'===$sibling) {
                    $sibling = '';
                }
                $parentBlock->insert($block, $sibling, true);
            } else {
                $parentBlock->append($block);
            }
        }

        if (!empty($node['output'])) {
            $method = (string)$node['output'];
            $this->addOutputBlock($blockName, $method);
        }
        
        Varien_Profiler::stop($_profilerKey);
        
        return $this;
    }
    
    protected function _generateAction($node, $parent)
    {
        $method = (string)$node['method'];
        if (!empty($node['block'])) {
            $parentName = (string)$node['block'];
        } else {
            $parentName = $parent->getBlockName();
        }
        
        $_profilerKey = 'BLOCK: '.$parentName.' -> '.$method;
        Varien_Profiler::start($_profilerKey);
        
        if (!empty($parentName)) {
            $block = $this->getBlock($parentName);
        }
        $args = (array)$node->children();
        unset($args['@attributes']);
        if (isset($node['json'])) {
            $json = explode(' ', (string)$node['json']);
            foreach ($json as $arg) {
                $args[$arg] = Zend_Json::decode($args[$arg]);
            }
        }
        if (isset($node['translate'])) {
            $items = explode(' ', (string)$node['translate']);
            foreach ($items as $arg) {
                $args[$arg] = __($args[$arg]);
            }
        }

        call_user_func_array(array($block, $method), $args);
        
        Varien_Profiler::stop($_profilerKey);
        
        return $this;
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
        return $this;
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
        #Mage::start(__METHOD__);

        if (!$className = Mage::getConfig()->getBlockClassName($type)) {
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
        elseif (isset($this->_blocks[$name])) {
            throw new Exception('Block with name "'.$name.'" already exists');
        }
        
        $block->setType($type)
            ->setName($name)
            ->setLayout($this)
            ->addData($attributes);
        
        $this->_blocks[$name] = $block;
        
        #Mage::stop(__METHOD__, true);

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
            
        $blockObj->setData('name', $blockName);
        $blockObj->setLayout($this);
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
        //$this->_output[] = array($blockName, $method);
        $this->_output[$blockName] = array($blockName, $method);
    }
    
    /**
     * Get all blocks marked for output
     *
     * @return array
     */
    public function getOutput()
    {
        $out = '';
        if (!empty($this->_output)) {
            foreach ($this->_output as $callback) {
                $out .= $this->getBlock($callback[0])->$callback[1]();
            }
        }
        return $out;
    }
    
    /**
     * Retrieve messages block
     *
     * @return Mage_Core_Block_Messages
     */
    public function getMessagesBlock()
    {
        if ($block = $this->getBlock('messages')) {
            return $block;
        }
        return $this->createBlock('core/messages', 'messages');
    }
}
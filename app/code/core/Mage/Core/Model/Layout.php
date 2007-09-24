<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Layout configuration class
 *
 */
class Mage_Core_Model_Layout extends Varien_Simplexml_Config
{
    /**
     * Pool of default package layout updates
     *
     * @var Mage_Core_Layout_Element
     */
    protected $_packageLayouts;

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

    /**
     * Layout area (f.e. admin, frontend)
     *
     * @var string
     */
    protected $_area;

    protected $_helpers = array();

    public function __construct($data=array())
    {
        $this->_elementClass = Mage::getConfig()->getModelClassName('core/layout_element');
        $this->setXml(simplexml_load_string('<layout/>', $this->_elementClass));
        parent::__construct($data);
    }

    /**
     * Set layout area
     *
     * @param   string $area
     * @return  Mage_Core_Model_Layout
     */
    public function setArea($area)
    {
    	$this->_area = $area;
    	return $this;
    }

    /**
     * Retrieve layout area
     *
     * @return string
     */
    public function getArea()
    {
    	return $this->_area;
    }

    public function init($cacheKey)
    {
        $package = Mage::getSingleton('core/design_package');
        $storeCode = Mage::getSingleton('core/store')->getCode();

        $this->setCacheId(($storeCode ? $storeCode.'_' : '')
            .$package->getArea().'_'
            .$package->getPackageName().'_'
            .$package->getTheme('layout').'_'
            .$cacheKey);

        $this->setCacheTags(array(
            'store:'.$storeCode,
            'theme:'.$package->getArea().'.'.$package->getPackageName().'.'.$package->getTheme('layout'),
        ));

        return $this;
    }

    public function getPackageLayoutUpdate($handle=null)
    {
        $layoutFilename = Mage::getSingleton('core/design_package')->getLayoutFilename('main.xml');
        if (empty($this->_packageLayouts)) {
            $updateStr = file_get_contents($layoutFilename);
            $updateStr = $this->processFileData($updateStr);
            $this->_packageLayouts = simplexml_load_string($updateStr, $this->_elementClass);
            if (empty($this->_packageLayouts)) {
                throw Mage::exception('Mage_Core', __('Could not load default layout file'));
            }
        }

        $this->updateCacheChecksum(filemtime($layoutFilename));

        if (is_null($handle)) {
            return $this->_packageLayouts;
        }
        if (!$this->_packageLayouts->$handle) {
            return false;
        }
        return $this->_packageLayouts->$handle;
    }

    public function getDatabaseLayoutUpdate($handle)
    {
        $package = Mage::getSingleton('core/design_package');
        
        try {
            $collection = Mage::getResourceModel('core/layout_collection')
                ->addPackageFilter($package->getPackageName())
                ->addThemeFilter($package->getTheme('layout'))
                ->addHandleFilter($handle)
                ->load();
    
            $this->updateCacheChecksum(Mage::getResourceModel('core/layout')->getTableChecksum());
            
            $updateStr = '';
            foreach ($collection->getIterator() as $update) {
                $updateStr .= $update->getLayoutUpdate();
            }
            $updateStr = $this->processFileData($updateStr);
            $updateXml = simplexml_load_string($updateStr);
        }
        catch (Exception $e){
            $updateXml = '';
        }
        return $updateXml;
    }

    /**
     * Load layout configuration update from file
     *
     * @param   string $fileName
     * @return  Mage_Core_Model_Layout
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

    /**
     * Merge layout update to current layout
     *
     * @param string|Mage_Core_Model_Layout_Element $update
     * @return Mage_Core_Model_Layout_Update
     */
    public function mergeUpdate($update)
    {
        if (!$update) {
            return $this;
        }

        if (is_string($update)) {
            $this->mergeUpdate($this->getPackageLayoutUpdate($update));
            $this->mergeUpdate($this->getDatabaseLayoutUpdate($update));
            return $this;
        }

        if (!$update instanceof Mage_Core_Model_Layout_Element) {
            throw Mage::exception('Mage_Core', __('Invalid layout update argument, expected Mage_Core_Model_Layout_Element'));
        }
        foreach ($update->children() as $child) {
            switch ($child->getName()) {
                case 'update':
                    $handle = (string)$child['handle'];
                    $this->mergeUpdate($this->getPackageLayoutUpdate($handle));
                    break;

                case 'remove':
                    if (isset($child['method'])) {
                        $this->removeAction((string)$child['name'], (string)$child['method']);
                    } else {
                        $this->removeBlock((string)$child['name']);
                    }
                    break;

                default:
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
            if (!$this->getCacheSaved()) {
                $this->saveCache();
            }
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
        if (!empty($block)) {
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
        }

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
     * @return    Mage_Core_Block_Abstract
     */
    public function createBlock($type, $name='', array $attributes = array())
    {
        if (!$className = Mage::getConfig()->getBlockClassName($type)) {
            Mage::throwException(__('Invalid block type: %s', $type));
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
            Mage::throwException(__('Block with name "%s" already exists', $name));
        }

        $block->setType($type)
            ->setName($name)
            ->addData($attributes)
            ->setLayout($this);

        $this->_blocks[$name] = $block;

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

    public function getHelper($type)
    {
        if (!isset($this->_helpers[$type])) {
            if (!$className = Mage::getConfig()->getBlockClassName($type)) {
                Mage::throwException(__('Invalid block type: %s', $type));
            }
            $helper = new $className();
            if ($helper) {
                if ($helper instanceof Mage_Core_Block_Abstract) {
                    $helper->setLayout($this);
                }
                $this->_helpers[$type] = $helper;
            }
        }
        return $this->_helpers[$type];
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
}

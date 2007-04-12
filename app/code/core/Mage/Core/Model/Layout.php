<?php

/**
 * Layout configuration class
 *
 */
class Mage_Core_Model_Layout extends Varien_Simplexml_Config
{
    protected $_subst = array('keys'=>array(), 'values'=>array());
    
    /**
     * Initialize layout configuration for $id key
     *
     * @param string $id
     */
    public function init($id)
    {
        $this->_elementClass = Mage::getConfig()->getModelClassName('core_model', 'layout_element');

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
            $this->_subst['keys'][] = '{'.$k.'}';
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
        $fileName = Mage::getBaseDir('layout', $moduleName).DS.$fileName;
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
        $layoutConfig = Mage::getConfig()->getXml()->$area->layouts->$id;
        if (!empty($layoutConfig)) {
            $updates = $layoutConfig->updates->children();
            foreach ($updates as $update) {
                $this->loadUpdate($update);
            }
        }
        return $this;
    }
    
    protected function _processCacheData($data)
    {
        $substKeys = array();
        $substValues = array();
        $subst = Mage::getConfig()->getPathVars();
        foreach ($subst as $k=>$v) {
            $substKeys[] = '{'.$k.'}';
            $substValues[] = $v;
        }
        return str_replace($substKeys, $substValues, $data);
    }
    
    /**
     * Create layout blocks from configuration
     *
     * @param Mage_Core_Layout_Element|null $parent
     */
    public function createBlocks($parent=null)
    {
        if (empty($parent)) {
            $parent = $this->getXml();
        }
        foreach ($parent as $node) {
            switch ($node->getName()) {
                case 'block':
                    $className = (string)$node['class'];
                    $blockName = (string)$node['name'];
                    $block = Mage::registry('blocks')->addBlock($className, $blockName);
                    
                    if (!empty($node['parent'])) {
                        $parentName = (string)$node['parent'];
                        $parent = Mage::registry('blocks')->getBlockByName($parentName);
                        
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
                        Mage::registry('blocks')->addOutputBlock($blockName, $method);
                    }
                    $this->createBlocks($node);
                    break;
                    
                case 'reference':
                    $this->createBlocks($node);
                    break;

                case 'action':
                    $name = (string)$node['block'];
                    $block = Mage::registry('blocks')->getBlockByName($name);
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
    
}
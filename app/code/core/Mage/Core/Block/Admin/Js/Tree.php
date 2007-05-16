<?php
/**
 * Extjs tree block
 *
 * @package    Mage
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Block_Admin_Js_Tree extends Mage_Core_Block_Admin_Js
{
    protected $_expandNodesStr = '';
    
    public function construct($container, $config=array())
    {
        //$config['loader'] = "new Tree.TreeLoader({dataUrl:'$loadUrl'})";

        $this->setAttribute('container', $container);
        $this->setAttribute('config', $config);
        
        /**
         * array(
         *      ['root'] => array(
         *          ['config']  => array()
         *          ['expanded']=> bool
         *          ['async']   => bool
         *          ['children'] => array(
         *              [$nodeName] => array(
         *                  ['config']  => array()
         *                  ['expanded']=> bool
         *                  ['async']   => bool
         *                  ['children']=> array()
         *              )
         *          )
         *      )
         * )
         */
        $this->setAttribute('nodes', array());
    }
    
    public function setRootNode($config)
    {
        $nodes = $this->getAttribute('nodes');
        if (isset($nodes['root']) && is_array($nodes['root'])) {
           $nodes['root']['config'] = $config;
        }
        else {
            $nodes['root'] = array();
            $nodes['root']['config']    = $config;
            $nodes['root']['children']  = array();
        }
        
        $nodes['root']['expanded']  = isset($config['expanded']) ? $config['expanded'] : true;
        $nodes['root']['async']     = isset($config['async']) ? $config['async'] : true;

        $this->setAttribute('nodes', $nodes);       
    }
    
    public function appendChild($childName, $config, $parentName='root')
    {
        $nodes = $this->getAttribute('nodes');
        if (!isset($nodes['root'])) {
            $nodes['root'] = array();
            $nodes['root']['config'] = array();
            $nodes['root']['children'] = array();
        }
        
        if (!$nodes=$this->_addNode($nodes, $childName, $config, $parentName)) {
            Mage::exception('Parent node "'.$parentName.'" not exists', 0, 'Core');
        }
        
        $this->setAttribute('nodes', $nodes);
    }
    
    protected function _addNode($nodes, $nodeName, $config, $parentName)
    {
        if (isset($nodes[$parentName])) {
            if (!isset($nodes[$parentName]['children'])) {
                $nodes[$parentName]['children'] = array();
            }
            
            $nodes[$parentName]['children'][$nodeName] = array();
            $nodes[$parentName]['children'][$nodeName]['expanded']  = isset($config['expanded']) ? $config['expanded'] : true;
            $nodes[$parentName]['children'][$nodeName]['async']     = isset($config['async']) ? $config['async'] : true;
            $nodes[$parentName]['children'][$nodeName]['config']    = $config;
            $nodes[$parentName]['children'][$nodeName]['children']  = array();
            return $nodes;
        }
        
        foreach ($nodes as $name=>$node) {
            if ($newnodes=$this->_addNode($node['children'], $nodeName, $config, $parentName)) {
                $nodes[$name]['children'] = $newnodes;
                return $nodes;
            }
        }
        return false;
    }
    
    protected function _nodesToJs($nodes, $namePrefix, $parent='')
    {
        $out = '';
        foreach ($nodes as $nodeName => $node) {
            $config = Zend_Json::encode($node['config']);
            $elementName = $namePrefix.'_'.$nodeName;
            if ($node['async']) {
                $out.= $this->setObjectJs($elementName, "new Ext.tree.AsyncTreeNode($config)");
            }
            else {
                $out.= $this->setObjectJs($elementName, "new Ext.tree.TreeNode($config)");
            }
            if (!empty($parent)) {
                $out.= $parent.'.appendChild('.$this->getObjectJs($elementName).')'.';';
            }
            if ($node['expanded']) {
                $this->_expandNodesStr.= $this->getObjectJs($elementName).'.expand();';
            }
            $out.= $this->_nodesToJs($node['children'], $elementName, $this->getObjectJs($elementName));
        }
        
        
        return $out;
    }
    
    public function toJs()
    {
        $container  = $this->getAttribute('container');
        $config     = Zend_Json::encode($this->getAttribute('config'));
        $nodes      = $this->getAttribute('nodes');
        $this->_expandNodesStr = '';
        
        $out = '';
        $out.= $this->setObjectJs('', "new Ext.tree.TreePanel('$container', $config)");
        $out.= $this->_nodesToJs($nodes, $this->getData('name')); 

        if (!empty($nodes['root'])) {
            $out.= $this->getObjectJs().'.setRootNode('.$this->getObjectJs($this->getData('name').'_root').');';
        }
        
        // TODO: object init in json
        $out = preg_replace("#\"\{\{(.*?)\}\}\"#", "\\1", $out);
        $out = stripslashes($out);
        $out.= $this->getObjectJs().'.render();';
        $out.= $this->_expandNodesStr;
        return $out;
    }
    
    public function toHtml()
    {
        $container  = $this->getAttribute('container');
        $out = '<div id="'.$container.'"></div>';
        $out.= parent::toHtml();
        return $out;
    }
}

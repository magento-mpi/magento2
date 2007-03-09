<?php

class Mage_Core_Block_Admin_Js_Tree extends Mage_Core_Block_Admin_Js
{
    function construct($container, $loadUrl, $config=array())
    {
        $config['loader'] = "new Tree.TreeLoader({dataUrl:'$loadUrl'})";

        $this->setAttribute('container', $container);
        $this->setAttribute('config', $config);
    }
    
    function setRootNode($config)
    {
        $config = Zend_Json::encode($config);
        $root = "new Tree.AsyncTreeNode($config)";
        $this->setAttribute('rootNode', $root);
    }
    
    function appendChild()
    {
        
    }
    
    function appendAsyncChild()
    {
        
    }
    
    function toJs()
    {
        $container  = $this->getAttribute('container');
        $config     = Zend_Json::encode($this->getAttribute('config'));
        $root       = $this->getAttribute('rootNode');
        
        $out = '';
        $out .= $this->setObjectJs('', "new Ext.tree.TreePanel($container, $config)");
        
        if (!empty($root)) {
            $out.= $this->setObjectJs($this->getInfo('name').'_root', $root);
            $out.= $this->getObjectJs().'.setRootNode('.$this->getObjectJs($this->getInfo('name').'_root').')';
        }
        return $out;
    }
}

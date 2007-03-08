<?php

abstract class Mage_Core_Block_Admin_Js_Layout extends Mage_Core_Block_Admin_Js
{
    function _getNewObjectJs()
    {
        $name = $this->_getObjectNameJs();
        $class = $this->getAttribute('jsClassName');
        $container = $this->getAttribute('container');
        $config = Zend_Json::encode($this->getAttribute('config'));
        
        $js = "$name = new $class($container, $config);\n";
        
        return $js;
    }
    
    function toJs()
    {
        $children = $this->getChild();
        foreach ($children as $block) {
            $out .= $block->toJs();
        }
        
        $out .= $this->_getNewObjectJs();
            
        return $out;
    }
}
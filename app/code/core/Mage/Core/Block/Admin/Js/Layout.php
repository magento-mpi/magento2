<?php

abstract class Mage_Core_Block_Admin_Js_Layout extends Mage_Core_Block_Admin_Js
{
    function getNewObjectJs()
    {
        $class = $this->getAttribute('jsClassName');
        $container = $this->getAttribute('container');
        $config = Zend_Json::encode($this->getAttribute('config'));
        
        $js = $this->setObjectJs('', "new $class($container, $config)");
        
        return $js;
    }
    
    function toJs()
    {
        $out = '';
        
        $config = $this->getAttribute('config');
        $children = $this->getChild();
        
        foreach ($children as $block) {
            $out .= $block->toJs();
        }
        
        if (!isset($config['isStub'])) {
            $out .= $this->getNewObjectJs();
        }
            
        return $out;
    }
}
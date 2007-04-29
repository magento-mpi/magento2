<?php

abstract class Mage_Core_Block_Admin_Js_Layout extends Mage_Core_Block_Admin_Js
{
    function getNewObjectJs()
    {
        $class = $this->getJsClassName();
        $container = $this->getAttribute('container');
        $config = $this->getAttribute('config');
        $jsConfig = Zend_Json::encode($config);
        
        $out = '';

        $out .= $this->setObjectJs('', "new $class($container, $jsConfig)");
        
        return $out;
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
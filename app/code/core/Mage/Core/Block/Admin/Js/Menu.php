<?php

class Mage_Core_Block_Admin_Js_Menu extends Mage_Core_Block_Admin_Js
{
    function construct()
    {
        
    }

    function addText()
    {
        
    }
    
    function addItem(Mage_Core_Block_Admin_Js_MenuItem $item) {
        
    }
    
    function addSeparator()
    {
        
    }
    
    function toJs()
    {
        $jsName = $this->getObjectNameJs();
        $config = Zend_Json::encode($this->getAttribute('config'));
        
        $out = "$jsName = new Ext.menu.Menu($config);\n";
        
        return $out;
    }
}
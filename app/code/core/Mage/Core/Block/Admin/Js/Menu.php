<?php

class Mage_Core_Block_Admin_Js_Menu extends Mage_Core_Block_Admin_Js
{
    function construct($config=array())
    {
        if (!isset($config['items'])) {
            $config['items'] = array();
        }
        $config['id'] = $this->getInfo('name');
        $this->setAttribute('config', $config);
    }
    
    function addItem($item) {
        $config = $this->getAttribute('config');
        $config['items'][] = $item;
        $this->setAttribute('config', $config);
    }
    
    function toJs()
    {
        $jsName = $this->getObjectNameJs();
        $config = $this->getAttribute('config');
        $jsConfig = Zend_Json::encode($this->stripItems($config));

        $out = '';
        $out .= "$jsName = new Ext.menu.Menu($jsConfig);\n";
        
        if (isset($config['items'])) {
            $out .= $this->getItemsJs($config['items']);
        }
        
        return $out;
    }
    
    function toString()
    {
        return $this->toJs();
    }
}
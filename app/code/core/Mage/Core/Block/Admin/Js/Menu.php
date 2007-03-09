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
        $config = $this->getAttribute('config');
        $jsConfig = Zend_Json::encode($this->stripItems($config));


        $out = '';
        if (!isset($config['isStub'])) {
            $out .= $this->setObjectJs('', "new Ext.menu.Menu($jsConfig)");
        }
        
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
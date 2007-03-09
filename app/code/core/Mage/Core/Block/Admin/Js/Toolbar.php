<?php

class Mage_Core_Block_Admin_Js_Toolbar extends Mage_Core_Block_Admin_Js
{ 
    function construct($container, $config=array())
    {
        $this->setAttribute('container', $container);
        $this->setAttribute('config', $config);
    }
    
    function toJs()
    {
        $jsName = $this->getObjectNameJs();
        $layout = $this->getObjectNameJs($this->getAttribute('container'));
        $region = $this->getAttribute('region');
        $container = "$layout.getRegion('$region').getEl().dom";
        $config = $this->getAttribute('config');
        $jsonConfig = Zend_Json::encode($this->stripItems($config));

        $out = '';
        $out .= "$jsName = new Ext.Toolbar(Ext.DomHelper.insertFirst($container,{tag:'div'},true),$jsonConfig);\n";
        
        if (isset($config['items'])) {
            $out .= $this->getItemsJs($config['items']);
        }
        return $out;
    }
}
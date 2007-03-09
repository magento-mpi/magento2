<?php

class Mage_Core_Block_Admin_Js_Toolbar extends Mage_Core_Block_Admin_Js
{ 
    function construct($container, $config=array())
    {
        $this->setAttribute('container', $container);
        $this->setAttribute('config', $config);
    }
    
    function addItem($item) {
        $config = $this->getAttribute('config');
        $config[] = $item;
        $this->setAttribute('config', $config);
    }

    function toJs()
    {
        $layout = $this->getObjectJs($this->getAttribute('container'));
        $region = $this->getAttribute('region');
        $container = "$layout.getRegion('$region').getEl().dom";
        $config = $this->getAttribute('config');
        $jsonConfig = Zend_Json::encode($this->stripItems($config));

        $out = '';
        $out .= $this->setObjectJs('', "new Ext.Toolbar(Ext.DomHelper.insertFirst($container,{tag:'div'},true),$jsonConfig)");

        return $out;
    }
}
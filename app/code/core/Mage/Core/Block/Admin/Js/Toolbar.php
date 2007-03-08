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
        $config = Zend_Json::encode($this->getAttribute('config'));
        
        $out = '';
        #$out .= "$layout.beginUpdate();\n";
        $out .= "$jsName = new Ext.Toolbar(Ext.DomHelper.insertFirst($container,{tag:'div'},true),$config);\n";
        #$out .= "$layout.endUpdate();\n";
        /*
        if (!empty($config['items']) && is_array($config['items'])) {
            foreach ($config['items'] as $item) {
                $itemConfig = Zend_Json::encode($item);
                $out .= "$jsName.add($itemConfig);\n";
            }
        }*/
        return $out;
    }
}
<?php

class Mage_Core_Block_Admin_Js_Toolbar extends Mage_Core_Block_Admin_Js
{ 
    function construct($container, $config=array())
    {
        $this->setAttribute('container', $container);
        $this->setAttribute('config', $config);
    }
    
    function getHandlersJs($items) 
    {
        $out = '';
        if (is_array($items)) {
            foreach ($items as $item) {
                if (isset($item['on']) && is_array($item['on'])) {
                    $id = $item['id'];
                    foreach ($item['on'] as $event=>$handlers) {
                        if (is_array($handlers)) {
                            foreach ($handlers as $handler) {
                                $function = $handler[0];
                                $arguments = Zend_Json::encode($handler[1]);
                                $out .= "Ext.EventManager.on(Ext.getEl('$id'), '$event', $function, $arguments);\n";
                            }
                        }
                    }
                }
                if (isset($item['menu'])) {
                    $out .= $this->getHandlersJs($item['menu']['items']);
                }
            }
        }
        return $out;
    }
    
    function toJs()
    {
        $jsName = $this->getObjectNameJs();
        $layout = $this->getObjectNameJs($this->getAttribute('container'));
        $region = $this->getAttribute('region');
        $container = "$layout.getRegion('$region').getEl().dom";
        $config = Zend_Json::encode($this->getAttribute('config'));

        $out = '';
        $out .= "$jsName = new Ext.Toolbar(Ext.DomHelper.insertFirst($container,{tag:'div'},true),$config);\n";
        $out .= $this->getHandlersJs($config);
        return $out;
    }
}
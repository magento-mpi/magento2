<?php

abstract class Mage_Core_Block_Admin_Js extends Mage_Core_Block_Abstract
{
    function getNewObjectJs()
    {
        return '';    
    }

    function getObjectNameJs($name='')
    {
        if (''===$name) {
            $name = $this->getInfo('name');
        }
        return "Ext.Mage.Collection['$name']";
    }
    
    function toJs()
    {
        return '';
    }
    
    function toString()
    {
        return $this->getObjectNameJs();
    }
    
    function getItemsJs($items)
    {
        $out = '';
        $jsName = $this->getObjectNameJs();
        if (is_array($items)) {
            foreach ($items as $item) {
                if ('-'===$item) {
                    $out .= "$jsName.addSeparator();\n";
                    continue;
                }
                $itemJsName = $this->getObjectNameJs($item['id']);
                $config = $item;
                unset($config['on']);
                $jsConfig = Zend_Json::encode($config);
                
                $out .= "$itemJsName = $jsName.add($jsConfig);\n";
                
                if (isset($item['on']) && is_array($item['on'])) {
                    $id = $item['id'];
                    foreach ($item['on'] as $event=>$handlers) {
                        if (is_array($handlers)) {
                            foreach ($handlers as $handler) {
                                $function = $handler[0];
                                $arguments = Zend_Json::encode($handler[1]);
                                $out .= "$itemJsName.on('$event', $function, $arguments);\n";
                            }
                        }
                    }
                }
                if (isset($item['menu']['items'])) {
                    $out .= $this->getItemsJs($item['menu']['items']);
                }
            }
        }
        return $out;
    }
    
    function stripItems($config) {
        unset($config['items']);
        return $config;
    }
    
}
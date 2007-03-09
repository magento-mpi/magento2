<?php

abstract class Mage_Core_Block_Admin_Js extends Mage_Core_Block_Abstract
{
    function getNewObjectJs()
    {
        return '';    
    }

    function setObjectJs($name, $value)
    {
        if (''===$name) {
            $name = $this->getInfo('name');
        }
        return "Mage.Collection.add('$name', $value);\n";
    }
    
    function getObjectJs($name='')
    {
        if (''===$name) {
            $name = $this->getInfo('name');
        }
        return "Mage.Collection.get('$name')";
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
        if (is_array($items)) {
            foreach ($items as $item) {
                if ('-'===$item) {
                    $out .= $this->getObjectJs().".addSeparator();\n";
                    continue;
                }
                $config = $item;
                unset($config['on']);
                $jsConfig = Zend_Json::encode($config);
                
                $out .= $this->setObjectJs($item['id'], $this->getObjectJs().".add($jsConfig)");
                
                if (isset($item['on']) && is_array($item['on'])) {
                    $id = $item['id'];
                    foreach ($item['on'] as $event=>$handlers) {
                        if (is_array($handlers)) {
                            foreach ($handlers as $handler) {
                                $function = $handler[0];
                                $arguments = Zend_Json::encode($handler[1]);
                                $out .= $this->getObjectJs($item['id']).".on('$event', $function, $arguments);\n";
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
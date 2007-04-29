<?php

class Mage_Core_Block_Admin_Js_Menu extends Mage_Core_Block_Admin_Js
{
    function construct($config=array())
    {
        if (!isset($config['items'])) {
            $config['items'] = array();
        }
        $config['id'] = $this->getData('name');
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
                                
                                if (!empty($handler[1])) {
                                    $scope = $handler[1];
                                } else {
                                    $scope = "this";
                                }
                                
                                if (!empty($handler[2])) {
                                    $arguments = Zend_Json::encode($handler[2]);
                                } else {
                                    $arguments = "[]";
                                }
                                $out .= $this->getObjectJs($item['id']).".on('$event', $function.createDelegate($scope, $arguments, true));\n";
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

    function toHtml()
    {
        return $this->toJs();
    }
}
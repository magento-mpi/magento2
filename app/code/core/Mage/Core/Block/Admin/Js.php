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
        //return $this->getObjectNameJs();
        return "<script type=\"text/javascript\" language=\"Javascript\">\n".$this->toJs()."</script>\n";
    }
    
    function stripItems($config) {
        unset($config['items']);
        return $config;
    }
    
}
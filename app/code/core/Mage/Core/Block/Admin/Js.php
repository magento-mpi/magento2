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
        return "Ext.Mage.Objects['$name']";
    }
    
    function toJs()
    {
        return '';
    }
    
    function toString()
    {
        return $this->getObjectNameJs();
    }
}
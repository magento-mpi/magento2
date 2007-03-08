<?php

abstract class Mage_Core_Block_Admin_Js extends Mage_Core_Block_Abstract
{
    function _getNewObjectJs()
    {
        return '';    
    }

    function _getObjectNameJs($name='')
    {
        if (''===$name) {
            $name = $this->getInfo('name');
        }
        return "Ext.Mage['$name']";
    }
    
    function toJs()
    {
        return '';
    }
    
    function toString()
    {
        return $this->_getObjectNameJs();
    }
}
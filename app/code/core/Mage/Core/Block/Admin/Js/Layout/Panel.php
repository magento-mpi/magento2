<?php

class Mage_Core_Block_Admin_Js_Layout_Panel extends Mage_Core_Block_Admin_Js_Layout
{
    function construct($container='', $config=array())
    {
        if (''===$container) {
            $container = "'".$this->getName()."'";
            $config['autoCreate'] = true;
        }
        $this->setAttribute('container', $container);
        $this->setAttribute('config', $config);
        
        $this->setJsClassName('Ext.ContentPanel');
    }

}
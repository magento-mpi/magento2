<?php

class Mage_Core_Block_Admin_Js_Layout_Content extends Mage_Core_Block_Admin_Js_Layout
{
    function construct($container, $config)
    {
        if (''===$container) {
            $container = $this->_getObjectNameJs();
            $config['autoCreate'] = true;
        }
        
        $this->setAttribute('container', $container);
        $this->setAttribute('config', $config);
        
        $this->setAttribute('jsClassName', 'Ext.ContentPanel');
    }
}
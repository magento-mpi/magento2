<?php

class Mage_Core_Block_Admin_Js_Layout_Grid extends Mage_Core_Block_Admin_Js_Layout
{
    function construct($container, $config)
    {
        $this->setAttribute('container', $container);
        $this->setAttribute('config', $config);
        $this->setAttribute('jsClassName', 'Ext.GridPanel');
    }
}
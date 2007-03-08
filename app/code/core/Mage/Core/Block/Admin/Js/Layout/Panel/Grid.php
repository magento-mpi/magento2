<?php

class Mage_Core_Block_Admin_Js_Layout_Panel_Grid extends Mage_Core_Block_Admin_Js_Layout_Panel
{
    function construct($container='', $config=array())
    {
        parent::construct($container, $config);
        
        $this->setAttribute('jsClassName', 'Ext.GridPanel');
    }
}
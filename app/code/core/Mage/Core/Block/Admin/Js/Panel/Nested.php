<?php

class Mage_Core_Block_Admin_Js_Layout_Nested extends Mage_Core_Block_Admin_Js_Layout_Content
{
    function construct($container, $config)
    {
        parent::construct($container, $config);
        
        $this->setAttribute('jsClassName', 'Ext.NestedLayoutPanel');
    }
}
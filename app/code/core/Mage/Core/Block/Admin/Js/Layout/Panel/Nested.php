<?php

class Mage_Core_Block_Admin_Js_Layout_Panel_Nested extends Mage_Core_Block_Admin_Js_Layout_Panel
{
    function construct($border='', $config=array())
    {
        if ($border instanceof Mage_Core_Block_Admin_Js_Layout_Border) {
            $block = $border;
            $border = $block->getObjectJs();
            $this->setChild($block->getName(), $block);
        }
        parent::construct($border, $config);
        
        $this->setJsClassName('Ext.NestedLayoutPanel');
    }
}
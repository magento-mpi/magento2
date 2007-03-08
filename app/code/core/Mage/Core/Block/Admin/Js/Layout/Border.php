<?php

class Mage_Core_Block_Admin_Js_Layout_Border extends Mage_Core_Block_Admin_Js_Layout
{    
    function construct($container, $config)
    {
        $this->setAttribute('container', $container);
        $this->setAttribute('config', $config);
        $this->setAttribute('jsClassName', 'Ext.BorderLayout');
    }
    
    function addRegion()
    {
        
    }
    
    function addPanel($target, $panel)
    {
        $regions = $this->getAttribute('regions');
        
        $regions[$target][] = $panel;
        
        $this->setAttribute('regions', $regions);
    }
    
    function toJs()
    {
        $out = parent::toJs();
        
        $out .= "Ext.Mage['$name'].beginUpdate();\n";
        foreach ($regions as $target=>$panels) {
            foreach ($panels as $panel) {
                $out .= "Ext.Mage['$name'].add('$target', $panel);\n";
            }
        }
        $out .= "Ext.Mage['$name'].endUpdate();\n";
        
        return $out;
    }
}
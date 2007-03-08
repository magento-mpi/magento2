<?php

class Mage_Core_Block_Admin_Js_Layout_Border extends Mage_Core_Block_Admin_Js_Layout
{    
    function construct($container, $config=array())
    {
        $this->setAttribute('container', $container);
        $this->setAttribute('config', $config);

        $this->setAttribute('jsClassName', 'Ext.BorderLayout');
    }
    
    function addPanel($target, $panel)
    {
        $regions = $this->getAttribute('regions');
        
        if ($panel instanceof Mage_Core_Block_Admin_Js_Layout_Panel) {
            $block = $panel;
            $name = $panel->getInfo('name');
        } else {
            $block = Mage_Core_Block::getBlockByName($panel);
            $name = $panel;
        }
        $this->setChild($name, $block);
        
        $regions[$target][] = $block->getObjectNameJs();
        
        $this->setAttribute('regions', $regions);
    }
    
    function addToolbar($target, $toolbar)
    {
        if ($toolbar instanceof Mage_Core_Block_Admin_Js_Toolbar) {
            $block = $toolbar;
            $name = $toolbar->getInfo('name');
        } else {
            $block = Mage_Core_Block::getBlockByName($toolbar);
            $name = $toolbar;
        }
        $this->setChild($name, $block);
        $block->setAttribute('region', $target);
        $block->setAttribute('outAfterParent', true);
    }
    
    function toJs()
    {
        $name = $this->getInfo('name');
        $jsName = $this->getObjectNameJs();
        $regions  = $this->getAttribute('regions');
        
        $out = '';
        
        $parent = $this->getInfo('parent');
        if (isset($parent) && ($parent['block'] instanceof Mage_Core_Block_Admin_Js_Layout_Panel_Nested)) {
            $container = $this->getObjectNameJs($this->getAttribute('container')).'.getEl()';
            $container = "Ext.DomHelper.append($container, {tag:'div'}, true)";
            $this->setAttribute('container', $container);
        }
        
        $out .= $this->getNewObjectJs();

        $children = $this->getChild();
        foreach ($children as $block) {
            if (!$block->getAttribute('outAfterParent')) {
                $out .= $block->toJs();
            }
        }
        
        $out .= "$jsName.beginUpdate();\n";
        if (!empty($regions) && is_array($regions)) {
            foreach ($regions as $target=>$panels) {
                foreach ($panels as $panel) {
                    $out .= "$jsName.add('$target', $panel);\n";
                }
            }
        }

        foreach ($children as $block) {
            if ($block->getAttribute('outAfterParent')) {
                $out .= $block->toJs();
            }
        }
        
        $out .= "$jsName.endUpdate();\n";
        
        return $out;
    }
    
    function toString()
    {
        if (isset($parent) && ($parent['block'] instanceof Mage_Core_Block_Admin_Js_Layout_Panel_Nested)) {
            return parent::toString();
        } else {
            return "<script type=\"text/javascript\" language=\"Javascript\">\n".$this->toJs()."</script>\n";
        }
    }
}
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
            $block = Mage::registry('blocks')->getBlockByName($panel);
            $name = $panel;
        }
        $this->setChild($name, $block);
        
        $regions[$target][] = $block;
        
        $this->setAttribute('regions', $regions);
    }
    
    function addToolbar($target, $toolbar)
    {
        if ($toolbar instanceof Mage_Core_Block_Admin_Js_Toolbar) {
            $block = $toolbar;
            $name = $toolbar->getInfo('name');
        } else {
            $block = Mage::registry('blocks')->getBlockByName($toolbar);
            $name = $toolbar;
        }
        $this->setChild($name, $block);
        $block->setAttribute('region', $target);
        $block->setAttribute('outAfterParent', true);
    }
    
    function addMenu($menu)
    {
        if ($menu instanceof Mage_Core_Block_Admin_Js_Menu) {
            $block = $menu;
            $name = $menu->getInfo('name');
        } else {
            $block = Mage::registry('blocks')->getBlockByName($menu);
            $name = $menu;
        }
        $this->setChild($name, $block);
        $this->setAttribute('outAfterParent', true);
    }
    
    function getUseExistingPanelJs($name, $js, $show=false)
    {        
        $getJs = $this->getObjectJs();
        
        if ($show) {
            $out = "if (!$getJs.showPanel('$name')) {\n$js\n}\n";
        } else {
            $out = "if (!$getJs.findPanel('$name')) {\n$js\n}\n";
        }
        
        return $out;
    }
    
    function toJs()
    {
        $name = $this->getInfo('name');
        $jsGetObject = $this->getObjectJs();
        $regions  = $this->getAttribute('regions');
        $config = $this->getAttribute('config');
        $uep = empty($config['useExistingPanels']) ? false : true;
        
        $out = '';
        
        $parent = $this->getInfo('parent');
        if (isset($parent) && ($parent['block'] instanceof Mage_Core_Block_Admin_Js_Layout_Panel_Nested)) {
            $container = $this->getObjectJs($this->getAttribute('container')).'.getEl()';
            $container = "Ext.DomHelper.append($container, {tag:'div'}, true)";
            $this->setAttribute('container', $container);
        }
        
        if (empty($config['isStub'])) {
            $out .= $this->getNewObjectJs();
        }

        $children = $this->getChild();
        foreach ($children as $block) {
            if (!$block->getAttribute('outAfterParent')) {
                $js = $block->toJs();
                if ($uep) {
                    $js = $this->getUseExistingPanelJs($block->getInfo('name'), $js, true);
                }
                $out .= $js;
            }
        }
        
        $out .= "$jsGetObject.beginUpdate();\n";
        if (!empty($regions) && is_array($regions)) {
            foreach ($regions as $target=>$panels) {
                foreach ($panels as $block) {
                    $panelJs = $block->getObjectJs();
                    $js = "$jsGetObject.add('$target', $panelJs);\n";
                    if ($uep) {
                        $js = $this->getUseExistingPanelJs($block->getInfo('name'), $js);
                    }
                    $out .= $js;
                }
            }
        }

        foreach ($children as $block) {
            if ($block->getAttribute('outAfterParent')) {
                $out .= $block->toJs();
            }
        }
        
        $out .= "$jsGetObject.endUpdate();\n";
        
        return $out;
    }
    
    function toString()
    {
        $parent = $this->getInfo('parent');
        if (isset($parent) && ($parent['block'] instanceof Mage_Core_Block_Admin_Js_Layout_Panel_Nested)) {
            return parent::toString();
        } else {
            return "<script type=\"text/javascript\" language=\"Javascript\">\n".$this->toJs()."</script>\n";
        }
    }
}
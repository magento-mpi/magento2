<?php

class Mage_Core_Config 
{
    const XPATH_ACTIVE_MODULES = "/config/module[active='true']";
    const XPATH_EXTENDS = "//*[@extends]";
    const XPATH_HANDLERS = "/config/module/load/*/configHandler";
    
    static private $_config = null;
    
    static function getConfig($xpath='')
    {
        if (''===$xpath) {
            return self::$_config;
        }
        return self::$_config->xpath($xpath);
    }
    
    static function load()
    {
        self::extend(self::loadFile('config.xml'));
        self::loadModules();
        self::applyExtends();
        self::processHandlers();
    }
    
    static function loadFile($fileName, $moduleName='')
    {
        if (''===$moduleName) {
            $root = Mage::getRoot('etc');
        } else {
            $root = Mage::getModuleInfo($moduleName)->getRoot('etc');
        }
        
        $xml = simplexml_load_file($root.DS.$fileName);
        
        return $xml;
    }

    static function loadModules()
    {
        $modules = self::getConfig(self::XPATH_ACTIVE_MODULES);
        
        foreach ($modules as $module) {
            self::extend(self::loadFile('config.xml', $module->name));
            break;
        }
    }
    
    static function applyExtends()
    {
        $targets = self::getConfig(self::XPATH_EXTENDS);
        
        foreach ($targets as $target) {
            $sources = self::getConfig((string)$target['extends']);
            foreach ($sources as $source) {
                foreach ($source->children() as $sourceKey=>$sourceNode) {
                    self::extend($sourceNode, $sourceKey, $target);
                }
            }
        }
    }
    
    static function processHandlers()
    {
        $handlers = self::getConfig(self::XPATH_HANDLERS);
        foreach ($handlers as $handler) {
            $nodes = self::getConfig($handler->xpath);
            
        }
    }
    
    static function extend($node, $key=null, $target=null)
    {
        if (is_null(self::$_config)) {
            // if self::$_config has never been set, initialize it and return
            self::$_config = $node;
            return true;
        }
        
        // this will be our new target node
        $childTarget = null;
        
        // here we have children of our source node
        $nodeChildren = $node->children();
        
        if (is_null($target)) {
            // if $target is not set append children of node to root and descend
            $childTarget = self::$_config;
        } elseif (!$nodeChildren && !isset($target->$key)) {
            // if node is string append only if not set at parent and return
            $target->addChild($key, (string)$node);
            return true;
        }
        
        if (isset($target->$key)) {
            // search for target child with same name subnode as node's name
            foreach ($target->$key as $targetKeyNode) {
                if ((string)$targetKeyNode->name==(string)$node->name) {
                    $childTarget = $targetKeyNode;
                    break;
                }
            }
        }
        
        if (is_null($childTarget)) {
            // if child target is not found create new and descend
            $childTarget = $target->addChild($key);
        }
        
        // finally add our source node children to resulting new target node
        foreach ($nodeChildren as $childKey=>$childNode) {
            self::extend($childNode, $childKey, $childTarget);
        }        
    }
    
}
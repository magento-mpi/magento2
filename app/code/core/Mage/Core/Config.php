<?php

class Mage_Core_Config
{
    const XPATH_ACTIVE_MODULES = "/config/module[active='true']";
    const XPATH_EXTENDS = "//*[@extends]";
    const XPATH_HANDLERS = "/config/module/load/*/configHandler";
    
    /**
     * Configuration xml
     *
     * @var Varien_Xml
     */
    protected $_xml = null;
    
    function __construct($sourceType='', $sourceData='', $moduleName='') {
        switch ($sourceType) {
            case 'file':
                $this->loadFile($sourceData, $moduleName);
                break;
                
            case 'string':
                $this->_xml = simplexml_load_string($sourceData);
                break;
                
            case 'dom':
                $this->_xml = simplexml_import_dom($sourceData);
                break;
        }
    }
    
    function getXpath($xpath='')
    {
        if (''===$xpath) {
            return $this->_xml;
        }
        return $this->_xml->xpath($xpath);
    }
    
    static function load()
    {
        $this->loadModules();
        $this->applyExtends();
        $this->processHandlers();
    }
    
    static function loadFile($fileName, $moduleName='')
    {
        if (''===$moduleName) {
            $rootDir = Mage::getRoot('etc');
        } else {
            $rootDir = Mage::getModuleInfo($moduleName)->getRoot('etc');
        }

        if (!is_readable($rootDir.DS.$fileName)) {
            Mage::exception('Can not read xml file '.$rootDir.DS.$fileName);
        }

        $this->_xml = simplexml_load_file($rootDir.DS.$fileName);
        
        return $this;
    }

    static function loadModules()
    {
        $modules = $this->getXpath(self::XPATH_ACTIVE_MODULES);
        
        foreach ($modules as $module) {
            $moduleConfig = new self('file', 'config.xml', $module->name);
            $this->_xml->extend($moduleConfig->getXpath(), true);
            break;
        }
    }
    
    static function applyExtends()
    {
        $targets = self::getConfig(self::XPATH_EXTENDS);
        
        foreach ($targets as $target) {
            $sources = $this->getXpath((string)$target['extends']);
            foreach ($sources as $source) {
                foreach ($source->children() as $sourceNode) {
                    $this->_xml->extend($sourceNode);
                }
            }
        }
    }
    
    static function processHandlers()
    {
        $handlers = $this->getXpath(self::XPATH_HANDLERS);
        foreach ($handlers as $handler) {
            $nodes = $this->_xml->getXpath($handler->xpath);
            
        }
    }
}
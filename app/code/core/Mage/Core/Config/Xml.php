<?php

class Mage_Core_Config_Xml
{
    const SIMPLEXML_CLASS = 'Varien_Xml';
    const XPATH_EXTENDS = "//*[@extends]";
    
    /**
     * Configuration xml
     *
     * @var Varien_Xml
     */
    protected $_xml = null;
    protected $_cacheDir = null;
    
    function __construct($sourceType='', $sourceData='') {
        $this->_cacheDir = Mage::getRoot('var').DS.'cache'.DS.'xml';
        
        $this->load($sourceType, $sourceData);
    }
    
    function load($sourceType='', $sourceData='') 
    {
        switch ($sourceType) {
            case 'xml':
                $this->_xml = $sourceData;
                break;
                
            case 'file':
                $this->_xml = $this->loadFile($sourceData);
                break;
                
            case 'string':
                $this->_xml = simplexml_load_string($sourceData, self::SIMPLEXML_CLASS);
                break;
                
            case 'dom':
                $this->_xml = simplexml_import_dom($sourceData, self::SIMPLEXML_CLASS);
                break;
        }
    }
    
    function getXml()
    {
        return $this->_xml;
    }
    
    function getXpath($xpath)
    {
        if (empty($this->_xml)) {
            return false;
        }

        if (!$result = @$this->_xml->xpath($xpath)) {
            return false;
        }

        return $result;
    }
    
    function loadFile($filePath)
    {
        if (!is_readable($filePath)) {
            Mage::exception('Can not read xml file '.$filePath);
        }

        return simplexml_load_file($filePath, self::SIMPLEXML_CLASS);
    }
    
    function saveFile($filePath)
    {
        $xmlText = $this->_xml->asXml();
        file_put_contents($filePath, $xmlText);
        
        return true;
    }

    function applyExtends()
    {
        $targets = $this->getXpath(self::XPATH_EXTENDS);
        if (!$targets) {
            return false;
        }
        
        foreach ($targets as $target) {
            $sources = $this->getXpath((string)$target['extends']);
            if (!$sources) {
                echo "Not found extend: ".(string)$target['extends'];
            }
            foreach ($sources as $source) {
                $target->extend($source);
            }
        }
        return true;
    }
    
    function cacheLoad($key)
    {
        $filePath = $this->_cacheDir.DS.$key.'.xml';
        if (is_readable($filePath)) {
            return $this->loadFile($filePath);
        } else {
            return false;
        }
    }
    
    function cacheSave($key)
    {
        $filePath = $this->_cacheDir.DS.$key.'.xml';
        $this->saveFile($filePath);
    }
}
<?php

class Varien_Simplexml_Config
{
    const SIMPLEXML_CLASS = 'Varien_Simplexml_Object';
    const XPATH_EXTENDS = "//*[@extends]";
    
    /**
     * Configuration xml
     *
     * @var Varien_Xml
     */
    protected $_xml = null;
    protected $_cacheKey = null;
    protected $_cacheStat = null;
    protected $_cacheDir = null;
    
    function __construct($sourceData='', $sourceType='xml') {
        $this->setXml($sourceType, $sourceData);
    }
    
    function setXml($sourceData, $sourceType='xml') 
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
            Zend::exception('Can not read xml file '.$filePath);
        }

        $xml = simplexml_load_file($filePath, self::SIMPLEXML_CLASS);
        
        return $xml;
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
            if ($sources) {
                foreach ($sources as $source) {
                    $target->extend($source);
                }
            } else {
                echo "Not found extend: ".(string)$target['extends'];
            }
            #unset($target['extends']);
        }
        return true;
    }
    
    function setCacheDir($dir)
    {
        
    }
    
    function setCacheKey($key)
    {
        $this->_cacheKey = $key;
        $this->_cacheStat = array();
    }

    function addCacheStat($fileName)
    {
        $this->_cacheStat[$fileName] = filemtime($fileName);
    }
    
    function getCacheFileName($key='')
    {
        if (''===$key) {
            $key = $this->_cacheKey;
        }

        return $this->_cacheDir.DS.$key.'.xml'; 
    }
    
    function getCacheStatFileName($key='')
    {
        if (''===$key) {
            $key = $this->_cacheKey;
        }

        return $this->_cacheDir.DS.$key.'.stat'; 
    }
    
    function loadCache($key='')
    {
        if (''===$key) {
            $key = $this->_cacheKey;
        }
        
        // get cache status file
        $statFile = $this->getCacheStatFileName($key);
        if (!is_readable($statFile)) {
            return false;
        }
        // read it
        $data = unserialize(file_get_contents($statFile));
        if (empty($data) || !is_array($data)) {
            return false;
        }
        // check that no source files were changed
        foreach ($data as $sourceFile=>$mtime) {
            if (filemtime($sourceFile)!==$mtime) {
                return false;
            }
        }
        // read cache file
        $cacheFile = $this->getCacheFileName($key);
        if (is_readable($cacheFile)) {
            return $this->loadFile($cacheFile);
        } else {
            return false;
        }
    }
    
    function saveCache($key='')
    {
        if (''===$key) {
            $key = $this->_cacheKey;
        }
        
        $statFile = $this->getCacheStatFileName($key);
        file_put_contents($statFile, serialize($this->_cacheStat));
        
        $cacheFile = $this->getCacheFileName($key);
        $this->saveFile($cacheFile);
    }
}

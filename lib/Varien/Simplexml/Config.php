<?php

class Varien_Simplexml_Config
{
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
    protected $_cacheLoaded = false;
    protected $_elementClass = 'Varien_Simplexml_Element';
    
    public function __construct($sourceData='', $sourceType='') {
        $this->setXml($sourceData, $sourceType);
    }
    
    public function getConstant($const)
    {
        return constant(get_class($this).'::'.$const);
    }
    
    public function isCacheLoaded()
    {
        return $this->_cacheLoaded;
    }
    
    public function setXml($sourceData, $sourceType='') 
    {
        if (''===$sourceType) {
            if ($sourceData instanceof SimpleXMLElement) {
                $sourceType = 'xml';
            } elseif ($sourceData instanceof DomNode) {
                $sourceType = 'dom';
            } elseif (is_string($sourceData)) {
                if (strlen($sourceData)<1000 && is_readable($sourceData)) {
                    $sourceType = 'file';
                } else {
                    $sourceType = 'string';
                }
            }
        }
        
        switch ($sourceType) {
            case 'xml':
                $this->_xml = $sourceData;
                break;
                
            case 'dom':
                $this->_xml = $this->loadDom($sourceData);
                break;
                
            case 'file':
                $this->_xml = $this->loadFile($sourceData);
                break;
                
            case 'string':
                $this->_xml = $this->loadString($sourceData);
                break;
        }
    }
    
    public function getXml()
    {
        return $this->_xml;
    }
    
    public function getXpath($xpath)
    {
        if (empty($this->_xml)) {
            return false;
        }

        if (!$result = @$this->_xml->xpath($xpath)) {
            return false;
        }

        return $result;
    }
    
    public function loadFile($filePath)
    {
        if (!is_readable($filePath)) {
            throw new Exception('Can not read xml file '.$filePath);
        }

        $fileData = file_get_contents($filePath);
        $fileData = $this->_processFileData($fileData);
        $xml = $this->loadString($fileData, $this->_elementClass);
        
        return $xml;
    }
    
    public function loadString($string)
    {
        $xml = simplexml_load_string($string, $this->_elementClass);
        
        return $xml;
    }
    
    public function loadDom($dom)
    {
        $xml = simplexml_import_dom($dom, $this->_elementClass);
        
        return $xml;
    }
    
    public function setKeyValue($key, $value, $overwrite=true)
    {
        $arr1 = explode('/', $key);
        $arr = array();
        foreach ($arr1 as $v) {
            if (!empty($v)) $arr[] = $v;
        }
        $last = sizeof($arr)-1;
        $xml = $this->_xml;
        foreach ($arr as $i=>$nodeName) {
            if ($last===$i) {
                if (!isset($xml->$nodeName) || $overwrite) {
                    $xml->$nodeName = $value;
                }
            } else {
                if (!isset($xml->$nodeName)) {
                    $xml = $xml->addChild($nodeName);
                } else {
                    $xml = $xml->$nodeName;
                }
            }

        }
        return $this;
    }
    
    public function saveFile($filePath)
    {
        $xmlText = $this->_xml->asXml();
        file_put_contents($filePath, $xmlText);
        
        return true;
    }

    public function applyExtends()
    {
        $targets = $this->getXpath($this->getConstant('XPATH_EXTENDS'));
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
                #echo "Not found extend: ".(string)$target['extends'];
            }
            #unset($target['extends']);
        }
        return true;
    }
    
    public function setCacheDir($dir)
    {
        $this->_cacheDir = $dir;
    }
    
    public function setCacheKey($key)
    {
        $this->_cacheKey = $key;
        $this->_cacheStat = array();
    }

    public function addCacheStat($fileName)
    {
        $this->_cacheStat[$fileName] = filemtime($fileName);
    }
    
    public function getCacheFileName($key='')
    {
        if (''===$key) {
            $key = $this->_cacheKey;
        }

        return $this->_cacheDir.DS.$key.'.xml'; 
    }
    
    public function getCacheStatFileName($key='')
    {
        if (''===$key) {
            $key = $this->_cacheKey;
        }

        return $this->_cacheDir.DS.$key.'.stat'; 
    }
    
    protected function _processFileData($text)
    {
        return $text;
    }
    
    public function loadCache($key='')
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
            $xml = $this->loadFile($cacheFile);
            if (!empty($xml)) {
                $this->_cacheLoaded = true;
                return $xml;
            }
        }
        return false;
    }
    
    public function saveCache($key='')
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

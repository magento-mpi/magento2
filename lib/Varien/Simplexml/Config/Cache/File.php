<?php

class Varien_Simplexml_Config_Cache_File extends Varien_Simplexml_Config_Cache_Abstract
{

    /**
     * Returns file name for cache file by key
     *
     * @param string $key
     * @return string
     */
    public function getFileName($key='')
    {
        return $this->getDir().DS.$this->getKey().'.xml';
    }

    /**
     * Returns file name for cache stats file
     *
     * @param string $key
     * @return string
     */
    public function getStatFileName()
    {
        return $this->getDir().DS.$this->getKey().'.stat';
    }

    public function addComponent($component)
    {
        $comps = $this->getComponents();
        $comps[$component] = array('mtime'=>filemtime($component));
        $this->setComponents($comps);
    }
    
    /**
     * Load configuration cache from file
     *
     * @param Varien_Simplexml_Config $config
     * @return Varien_Simplexml_Element
     */
    public function load()
    {
        $this->setIsLoaded(false);
        
        // get cache status file
        $statFile = $this->getStatFileName($this->getKey());
        if (!is_readable($statFile)) {
            return false;
        }
        // read it and validate
        $data = unserialize(file_get_contents($statFile));
        if (empty($data) || !is_array($data) || !$this->validateComponents($data)) {
            return false;
        }
        
        // read cache file
        $cacheFile = $this->getFileName($this->getKey());
        if (is_readable($cacheFile)) {
            $xml = file_get_contents($cacheFile);
            if (!empty($xml)) {
                $this->getConfig()->setXml($xml);
                $this->setIsLoaded(true);
                return true;
            }
        }
        return false;
    }
    
    public function save()
    {
        if (!$this->getIsAllowedToSave()) {
            return false;
        }
        
        $statFile = $this->getStatFileName($this->getKey());
        file_put_contents($statFile, serialize($this->getComponents()));

        $cacheFile = $this->getFileName($this->getKey());
        file_put_contents($cacheFile, $this->getConfig()->getNode()->asNiceXml());
        
        return true;
    }
}
<?php

class Mage_Core_Layout extends Varien_Simplexml_Config
{
    const SIMPLEXML_CLASS = 'Mage_Core_Layout_Object';
    
    public function init($event)
    {
        $this->setCacheDir(Mage::getBaseDir('var').DS.'cache'.DS.'xml');
        $this->setCacheKey($event);
        
        if ($xml = $this->loadCache()) {
            $this->setXml($xml);
        } else {
            $this->setXml('<layout/>');
            Mage::dispatchEvent($event);
            $this->saveCache();
        }
    }
    
    public function loadUpdateFile($fileName)
    {
        $update = $this->loadFile($fileName);
        $this->getXml()->appendChild($update);
    }
    
    public function prepare()
    {
        $children = $this->children();
        foreach ($children as $childNodes) {
            foreach ($childNodes as $child) {
                $child->setParent($this);
                $child->prepare($args);
            }
        }
        return $this;
    }
    
    
    
}
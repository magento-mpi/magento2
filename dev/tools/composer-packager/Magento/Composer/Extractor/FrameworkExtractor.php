<?php

namespace Magento\Composer\Extractor;

class FrameworkExtractor extends  BaseExtractor{

    private $_path;

    public function __construct($rootDir, $logger){
        parent::__construct($logger);
        $this->_path = $rootDir . '/lib/Magento/';
    }

    public function getPath(){
        return $this->_path;
    }

    public function getType(){
        return "magento2-framework";
    }

    public function getParser($filename){
        return new \Magento\Composer\Parser\FrameworkXmlParser($filename);
    }

    public function createComponent($name){
        return new \Magento\Composer\Model\Framework($name);
    }

    public function setValues(&$component, \Magento\Composer\Model\ArrayAndObjectAccess $definition){
        $component->setVersion($definition->version);
        $component->setLocation($definition->location);
        $component->setType($this->getType());
        return $component;
    }

}
<?php

namespace Magento\Composer\Extractor;

class ModuleExtractor extends  BaseExtractor{

    private $_path = 'app/code/Magento/';

    public function __construct($rootDir, $logger){
        parent::__construct($logger);
        $this->_path = $rootDir . '/app/code/Magento/';
    }

    public function getPath(){
        return $this->_path;
    }

    public function getType(){
        return "magento2-module";
    }

    public function getParser($filename){
        return new \Magento\Composer\Parser\ModuleXmlParser($filename);
    }

    public function createComponent($name){
        return new \Magento\Composer\Model\Module($name);
    }

    public function setValues(&$component, \Magento\Composer\Model\ArrayAndObjectAccess $definition){
        $component->setVersion($definition->version);
        $component->setActive($definition->active);
        $component->setLocation($definition->location);
        $component->setType($this->getType());
        return $component;
    }

}
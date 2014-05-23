<?php

namespace Magento\Composer\Extractor;

class AdminThemeExtractor extends  BaseExtractor{

    protected  $_path = 'app/design/adminhtml/Magento/';

    public function __construct($rootDir, $logger){
        parent::__construct($logger);
        $this->_path = $rootDir . '/app/design/adminhtml/Magento/';
    }

    public function getPath(){
        return $this->_path;
    }

    public function getType(){
        return "magento2-theme-adminhtml";
    }

    public function getParser($filename){
        return new \Magento\Composer\Parser\ThemeXmlParser($filename);
    }

    public function createComponent($name){
       return new \Magento\Composer\Model\Theme($name);
    }

    public function setValues(&$component, \Magento\Composer\Model\ArrayAndObjectAccess $definition){
        $component->setVersion($definition->version);
        $component->setLocation($definition->location);
        $component->setType($this->getType());
        return $component;
    }

}
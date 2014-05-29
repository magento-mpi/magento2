<?php

namespace Magento\Composer\Extractor;

class LanguagePackExtractor extends  BaseExtractor{

    private $_path = 'app/i18n/Magento/';

    public function __construct($rootDir, $logger){
        parent::__construct($logger);
        $this->_path = $rootDir . '/app/i18n/Magento/';
    }

    public function getPath(){
        return $this->_path;
    }

    public function getType(){
        return "magento2-language";
    }

    public function getParser($filename){
        return new \Magento\Composer\Parser\LanguagePackXmlParser($filename);
    }

    public function createComponent($name){
        return new \Magento\Composer\Model\LanguagePack($name);
    }

    public function setValues(&$component, \Magento\Composer\Model\ArrayAndObjectAccess $definition){
        $component->setVersion($definition->version);
        $component->setLocation($definition->location);
        $component->setType($this->getType());
        return $component;
    }

}
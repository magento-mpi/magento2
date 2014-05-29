<?php

namespace Magento\Composer\Extractor;

class LibraryExtractor extends  BaseExtractor{

    private $_collection = array();
    private $_path;

    public function __construct($rootDir, $logger){
        parent::__construct($logger);
        $this->_path = $rootDir . '/lib/';
    }

    public function extract($components = array(), &$count = 0){
        $this->_counter = &$count;
        $this->_counter = 0;
        $this->addToCollection($components);
        $parser = $this->getParser($this->getPath());
        $definition = $parser->getMappings();
        $this->createAndAdd($definition);
        return $this->getCollection();
    }

    public function getPath(){
        return $this->_path;
    }

    public function getType(){
        return "magento2-library";
    }

    public function getParser($filename){
        return new \Magento\Composer\Parser\LibraryXmlParser($filename);
    }

    public function createComponent($name){
        return new \Magento\Composer\Model\Library($name);
    }

    public function setValues(&$component, \Magento\Composer\Model\ArrayAndObjectAccess $definition){
        $component->setVersion($definition->version);
        $component->setLocation($definition->location);
        $component->setType($this->getType());
        return $component;
    }

}
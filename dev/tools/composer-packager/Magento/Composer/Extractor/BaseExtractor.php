<?php

namespace Magento\Composer\Extractor;

abstract class BaseExtractor implements  \Magento\Composer\Extractor{

    private $_collection = array();
    private $_logger;
    protected  $_counter;

    public function __construct(\Magento\Composer\Log\Log $logger){
        $this->_logger = $logger;
    }

    public function extract($collection = array(), &$count = 0){
        $this->_counter = &$count;
        $this->_counter = 0;
        $this->addToCollection($collection);

        foreach (new \DirectoryIterator($this->getPath()) as $component) {
            if ($component->isDot()) { continue; }
            if ($component->isDir()) {
                $parser = $this->getParser($this->getPath() . $component->getFilename() );
                $definition = $parser->getMappings();
                $this->createAndAdd($definition);
            }
        }
        return $this->getCollection();
    }

    public function addToCollection($collection){
            if(!is_null($collection) && sizeof($collection) > 0){
                $this->_collection = array_merge($this->_collection, $collection);
            }
    }

    public function getCollection(){
        return $this->_collection;
    }

    public function createAndAdd(\Magento\Composer\Model\ArrayAndObjectAccess $definition)
    {
        $name = \Magento\Composer\Helper\Converter::nametoVendorPackage($definition->name);
        //Check if module already exists.
        if (array_key_exists($name, $this->_collection)) {
            //Add version and other info to it.
            $component = $this->_collection[$name];
        } else {
            $component = $this->createComponent($name);
        }
        $this->_counter++;
        $this->setValues($component, $definition);
        $this->_collection[$name] = $component;
        if(isset($definition->dependencies) && !empty($definition->dependencies)){
            foreach ($definition->dependencies as $dependency) {
                $name = \Magento\Composer\Helper\Converter::nametoVendorPackage($dependency);
                //Check if Module instance already exists.
                if (array_key_exists($name, $this->_collection)) {
                    //Already exists.
                    $dependentComponent = $this->_collection[$name];
                } else {
                    //Make a new one
                    $dependentComponent = $this->createComponent($name);
                    $this->_collection[$name] = $dependentComponent;
                }
                $component->addDependencies($dependentComponent);
            }
        }
        $this->_logger->debug("Extracted Component %-40s [%9s] with %2d dependencies", $component->getName(), $component->getVersion(), sizeof($component->getDependencies()));
        return $component;
    }



}
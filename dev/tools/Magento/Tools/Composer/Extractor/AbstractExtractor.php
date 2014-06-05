<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Extractor;

use \Magento\Tools\Composer\ExtractorInterface;
use \Magento\Tools\Composer\Helper\Converter;
use \Magento\Tools\Composer\Model\ArrayAndObjectAccess;
use \Magento\Tools\Composer\Model\Package;

abstract class AbstractExtractor implements  ExtractorInterface
{

    protected  $_collection = array();
    private $_logger;
    protected  $_counter;
    protected  $_path;

    public function __construct($rootDir, \Zend_Log $logger)
    {
        $this->_logger = $logger;
        $this->setPath($rootDir. $this->getSubPath());
    }

    public function getPath()
    {
        return $this->_path;
    }

    public function setPath($path)
    {
        $this->_path = $path;
    }

    public abstract function getType();

    public abstract function getSubPath();

    public abstract function getParser($filename);

    public function extract($collection = array(), &$count = 0)
    {
        $this->_counter = &$count;
        $this->_counter = 0;
        $this->addToCollection($collection);

        foreach (new \DirectoryIterator($this->getPath()) as $component) {
            if ($component->isDot()) {
                continue;
            }
            if ($component->isDir()) {
                $parser = $this->getParser($this->getPath() . $component->getFilename() );
                $definition = $parser->getMappings();
                $this->create($definition);
            }
        }
        return $this->_collection;
    }

    public function addToCollection($collection)
    {
        if (!is_null($collection) && sizeof($collection) > 0) {
            $this->_collection = array_merge($this->_collection, $collection);
            }
    }

    public function create(ArrayAndObjectAccess $definition)
    {
        $name = Converter::nametoVendorPackage($definition->name);
        $component = $this->checkAndCreate($name);
        $this->setValues($component, $definition);
        $this->_collection[$name] = $component;
        $this->_counter++;

        if (isset($definition->dependencies) && !empty($definition->dependencies)) {
            foreach ($definition->dependencies as $dependency) {
                $dependentComponentName = Converter::nametoVendorPackage($dependency);
                $dependentComponent = $this->checkAndCreate($dependentComponentName);
                $this->_collection[$dependentComponentName] = $dependentComponent;
                $component->addDependencies($dependentComponent);
            }
        }
        $this->_logger->log(sprintf("Extracted Component %-40s [%7s] with %2d dependencies",
            $component->getName(), $component->getVersion(), sizeof($component->getDependencies())), \Zend_Log::DEBUG);
        return $component;
    }

    public function setValues(&$component, ArrayAndObjectAccess $definition)
    {
        $component->setVersion($definition->version);
        $component->setLocation($definition->location);
        $component->setType($this->getType());
        return $component;
    }

    public function checkAndCreate($componentName)
    {
        //Check if module already exists.
        if (array_key_exists($componentName, $this->_collection)) {
            //Add version and other info to it.
            $component = $this->_collection[$componentName];
        } else {
            $component = new Package($componentName);
        }
        return $component;
    }




}
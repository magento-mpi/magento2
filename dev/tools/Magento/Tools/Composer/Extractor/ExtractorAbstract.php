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
use \Magento\Tools\Composer\Model\Package;
use \Magento\Tools\Composer\ParserInterface;

/**
 * Abstract Extractor For Composer Packages
 */
abstract class ExtractorAbstract implements  ExtractorInterface
{

    /**
     * Collection of Packages
     *
     * @var array
     */
    protected  $_collection = array();

    /**
     * Application Logger
     *
     * @var \Zend_Log
     */
    protected $_logger;

    /**
     * Counter for created components
     *
     * @var int
     */
    protected  $_counter;

    /**
     * Root Directory
     *
     * @var string
     */
    protected $_rootDir;

    /**
     * Parser related to each extractor
     *
     * @var \Magento\Tools\Composer\ParserInterface
     */
    protected $_parser;

    /**
     * Location of component
     *
     * @var string
     */
    protected  $_path;

    /**
     * Extractor Constructor
     *
     * @param string $rootDir
     * @param \Zend_Log $logger
     * @param ParserInterface $parser
     */
    public function __construct($rootDir, \Zend_Log $logger, ParserInterface $parser)
    {
        $this->_logger = $logger;
        $this->_rootDir = $rootDir;
        $this->_parser = $parser;
    }

    /**
     * {@inheritdoc}
     */
    public abstract function getType();

    /**
     * {@inheritdoc}
     */
    public function extract(array $collection = array(), &$count = 0)
    {
        $this->_counter = &$count;
        $this->_counter = 0;
        $this->addToCollection($collection);

        foreach (new \DirectoryIterator($this->_rootDir . $this->getPath()) as $component) {
            if ($component->isDot()) {
                continue;
            }
            if ($component->isDir()) {
                $definition = $this->_parser->getMappings($this->_rootDir, $this->getPath().$component->getFilename());
                $this->create($definition);
            }
        }
        return $this->_collection;
    }

    /**
     * Merges Packages to Collection
     *
     * @param array $collection
     */
    public function addToCollection($collection)
    {
        if (!is_null($collection) && sizeof($collection) > 0) {
            $this->_collection = array_merge($this->_collection, $collection);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $definition)
    {
        $name = Converter::nametoVendorPackage($definition['name']);
        $component = $this->checkAndCreate($name);
        $this->setValues($component, $definition);
        $this->_collection[$name] = $component;
        $this->_counter++;

        if (!empty($definition['dependencies'])) {
            foreach ($definition['dependencies'] as $dependency) {
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

    /**
     * {@inheritdoc}
     */
    public function setValues(Package &$component, array $definition)
    {
        $component->setVersion($definition['version']);
        $component->setLocation($definition['location']);
        $component->setType($this->getType());
        return $component;
    }

    /**
     * Creates or Retrieves Package from Collection
     *
     * @param string $componentName
     * @return Package
     */
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
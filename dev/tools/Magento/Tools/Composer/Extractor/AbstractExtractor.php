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

/**
 * Abstract Extractor For Composer Packages
 */
abstract class AbstractExtractor implements ExtractorInterface
{

    /**
     * Collection of Packages
     *
     * @var array
     */
    protected $_collection = array();

    /**
     * Application Logger
     *
     * @var \Zend_Log
     */
    private $_logger;

    /**
     * Counter for created components
     *
     * @var int
     */
    protected $_counter;

    /**
     * Location of component
     *
     * @var string
     */
    protected $_path;

    /**
     * Extractor Constructor
     *
     * @param string $rootDir
     * @param \Zend_Log $logger
     */
    public function __construct($rootDir, \Zend_Log $logger)
    {
        $this->_logger = $logger;
        $this->setPath($rootDir. $this->getSubPath());
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Set Location of Component
     *
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->_path = $path;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getType();

    /**
     * {@inheritdoc}
     */
    abstract public function getSubPath();

    /**
     * {@inheritdoc}
     */
    abstract public function getParser($filename);

    /**
     * {@inheritdoc}
     */
    public function extract(array $collection = array(), &$count = 0)
    {
        $this->_counter = &$count;
        $this->_counter = 0;
        $this->addToCollection($collection);

        foreach (new \DirectoryIterator($this->getPath()) as $component) {
            if ($component->isDot()) {
                continue;
            }
            if ($component->isDir()) {
                $parser = $this->getParser($this->getPath() . $component->getFilename());
                $definition = $parser->getMappings();
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

        if (isset($definition['dependencies']) && !empty($definition['dependencies'])) {
            foreach ($definition['dependencies'] as $dependency) {
                $dependentComponentName = Converter::nametoVendorPackage($dependency);
                $dependentComponent = $this->checkAndCreate($dependentComponentName);
                $this->_collection[$dependentComponentName] = $dependentComponent;
                $component->addDependencies($dependentComponent);
            }
        }
        $this->_logger->log(
            sprintf(
                "Extracted Component %-40s [%7s] with %2d dependencies",
                $component->getName(),
                $component->getVersion(),
                sizeof($component->getDependencies())
            ),
            \Zend_Log::DEBUG
        );
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

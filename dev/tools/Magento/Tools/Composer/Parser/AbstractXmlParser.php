<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Parser;

use \Magento\Tools\Composer\ParserInterface;

/**
 * Abstract XML Parser for magento components
 */
abstract class AbstractXmlParser implements ParserInterface
{

    /**
     * Component Directory Location
     *
     * @var string
     */
    protected $_componentDir;

    /**
     * Component Identifier File
     *
     * @var \SplFileObject
     */
    protected $_file;

    /**
     * AbstractXmlParser constructor
     *
     * @param string $componentDir
     */
    public function __construct($componentDir)
    {
        $this->_componentDir = $componentDir;
        $this->setFile($this->_componentDir.$this->getSubPath());
    }

    /**
     * Create an array object with component information
     *
     * @param string $name
     * @param string $version
     * @param string $location
     * @param array $dependencies
     * @return array
     */
    public function createDefinition($name, $version, $location, array $dependencies)
    {
        $definition = array();
        $definition['name'] = $name;
        $definition['version'] = $version;
        $definition['location'] = $location;
        $definition['dependencies'] = $dependencies;
        return $definition;
    }

    /**
     * Retrieve Sub Path for Component
     * @return string
     */
    public abstract function getSubPath();

    /**
     * Maps XML file information and presents back into array
     *
     * @throws \ErrorException
     * @return array
     */
    protected abstract function _parseMappings();

    /**
     * Retrieves Component Directory Location
     * @return string
     */
    public function getComponentDir()
    {
        return $this->_componentDir ;
    }

    /**
     * Generates a \SplFileObject of the given location
     *
     * @param string|\SplFileObject $file
     * @return $this
     */
    protected function setFile($file)
    {
        if (is_string($file)) {
            $file = new \SplFileObject($file);
        }
        $this->_file = $file;
        return $this;
    }

    /**
     * Retrieve component identifier file
     *
     * @return \SplFileObject
     */
    public function getFile()
    {
        return $this->_file;
    }


    /**
     * {@inheritdoc}
     */
    public function getMappings()
    {
        $file = $this->getFile();

        if (!$file->isReadable()) {
            throw new \ErrorException(sprintf('Component file "%s" not readable', $file->getPathname()));
        }

        $map = $this->_parseMappings();
        return $map;
    }


}
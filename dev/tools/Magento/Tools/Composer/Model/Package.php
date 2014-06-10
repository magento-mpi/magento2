<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Model;

/**
 * Package model
 */
class Package
{
    /**
     * Name of package
     *
     * @var string
     */
    private $_name;

    /**
     * Version of package
     *
     * @var string
     */
    private $_version;

    /**
     * Location of Package
     *
     * @var string
     */
    private $_location;

    /**
     * Array of Dependencies
     * @var array|null
     */
    private $_dependencies =null;

    /**
     * Type of Package
     * @var string
     */
    private $_type;

    /**
     * Package Constructor
     *
     * @param string $name
     * @param string|null $version
     * @param string|null $location
     * @param string|null $type
     */
    public function __construct($name, $version = null, $location = null, $type = null)
    {
        $this->_name = $name;
        $this->setVersion($version);
        $this->setLocation($location);
        $this->setType($type);
        $this->_dependencies = array();
    }

    /**
     * Setter for Type
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    /**
     * Get type of package
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Get name of package
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Returns Version of Package
     * @return string
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * Returns Location of Package
     * @return string
     */
    public function getLocation()
    {
        return $this->_location;
    }

    /**
     * Sets Version of Package
     * @param string $version
     * @return $this
     */
    public function setVersion($version)
    {
        $this->_version = $version;
        return $this;
    }

    /**
     * Set location of package
     * @param string $location
     * @return $this
     */
    public function setLocation($location)
    {
        $this->_location = $location;
        return $this;
    }

    /**
     * Adds one or more dependencies to package
     * @param string|array $dependencies
     * @return $this
     */
    public function addDependencies($dependencies)
    {
        if (!is_array($dependencies)) {
            $dependencies = array($dependencies);
        }
        $this->_dependencies = array_merge($this->_dependencies, $dependencies);
        return $this;
    }

    /**
     * Returns all dependencies of package
     * @return array|null
     */
    public function getDependencies()
    {
        return $this->_dependencies;
    }
}

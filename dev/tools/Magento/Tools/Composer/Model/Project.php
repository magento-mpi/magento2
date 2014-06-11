<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Model;

/**
 * Project model
 */
class Project extends Package
{

    private $_excludes = array();

    /**
     * Package Constructor
     *
     * @param string $name
     * @param string|null $version
     * @param string|null $location
     * @param string|null $type
     */
    public function __construct($name, $version = null, $location=null, $type=null, $excludes = array())
    {
        parent::__construct($name, $version, $location, $type);
        $this->_excludes = $excludes;
    }

    /**
     * Adds one or more excludes to package locations
     *
     * @param string|array $mappings
     * @return $this
     */
    public function addExcludes($mappings)
    {
        if (!is_array($mappings)) {
            $mappings = array($mappings);
        }
        $this->_excludes = array_merge($this->_locationMappings, $mappings);
        return $this;
    }

    /**
     * Returns all Excludes Mappings
     *
     * @return array
     */
    public function getExcludes()
    {
        return $this->_excludes;
    }
}
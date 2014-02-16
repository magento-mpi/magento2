<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

/**
 * Association of arbitrary properties with a list of page assets
 */
class PropertyGroup extends Collection
{
    /**
     * @var array
     */
    protected $properties = array();

    /**
     * @param array $properties
     */
    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    /**
     * Retrieve values of all properties
     *
     * @return array()
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Retrieve value of an individual property
     *
     * @param string $name
     * @return mixed
     */
    public function getProperty($name)
    {
        return isset($this->properties[$name]) ? $this->properties[$name] : null;
    }
}

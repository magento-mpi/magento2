<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Association of arbitrary properties with a list of page assets
 */
namespace Magento\Page\Model\Asset;

class PropertyGroup extends \Magento\Core\Model\Page\Asset\Collection
{
    /**
     * @var array
     */
    private $_properties = array();

    /**
     * @param array $properties
     */
    public function __construct(array $properties)
    {
        $this->_properties = $properties;
    }

    /**
     * Retrieve values of all properties
     *
     * @return array()
     */
    public function getProperties()
    {
        return $this->_properties;
    }

    /**
     * Retrieve value of an individual property
     *
     * @param string $name
     * @return mixed
     */
    public function getProperty($name)
    {
        return isset($this->_properties[$name]) ? $this->_properties[$name] : null;
    }
}

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
class Magento_Page_Model_Asset_PropertyGroup extends Magento_Core_Model_Page_Asset_Collection
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

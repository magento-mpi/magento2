<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Page_Model_Asset_Group extends Mage_Core_Model_Asset_Collection
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
     * Retrieve value of a property
     *
     * @param string $name
     * @return mixed|null
     */
    public function getProperty($name)
    {
        return isset($this->_properties[$name]) ? $this->_properties[$name] : null;
    }
}

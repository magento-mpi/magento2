<?php
/**
 * Resource configuration
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */ 
class Mage_Core_Model_Config_Resource
{
    public function __construct(Mage_Core_Model_Config_Primary $config)
    {
$this->_config = $config;
    }

    /**
     * Retrieve resource connection configuration by name
     *
     * @param $name
     * @return Varien_Simplexml_Element
     */
    public function getResourceConnectionConfig($name)
    {
        return $this->_config->getResourceConnectionConfig($name);

    }

    /**
     * Retrieve reosurce type configuration
     *
     * @param $type
     * @return Varien_Simplexml_Element
     */
    public function getResourceTypeConfig($type)
    {
        return $this->_config->getResourceTypeConfig($type);
    }

    /**
     * Retrieve database table prefix
     *
     * @return string
     */
    public function getTablePrefix()
    {
    }

}

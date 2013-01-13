<?php
/**
 * Primary application config (app/etc/*.xml)
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Primary extends Mage_Core_Model_Config_Base
{
    /**
     * @param Mage_Core_Model_Config_Loader_Primary $loader
     */
    public function __construct(Mage_Core_Model_Config_Loader_Primary $loader)
    {
        parent::__construct('<config/>');
        $loader->load($this);
    }

    public function getResourceConnectionModel()
    {
        return $this->getResourceConnectionConfig('core_setup')->model;
    }

    /**
     * Get connection configuration
     *
     * @param   string $name
     * @return  Varien_Simplexml_Element
     */
    public function getResourceConnectionConfig($name)
    {
        $config = $this->getResourceConfig($name);
        if ($config) {
            $conn = $config->connection;
            if ($conn) {
                if (!empty($conn->use)) {
                    return $this->getResourceConnectionConfig((string)$conn->use);
                } else {
                    return $conn;
                }
            }
        }
        return false;
    }

    /**
     * // After Modules
     * Get resource configuration for resource name
     *
     * @param string $name
     * @return Varien_Simplexml_Object
     */
    public function getResourceConfig($name)
    {
        return $this->_xml->global->resources->{$name};
    }

    /**
     * // After Modules
     * Get resource configuration for resource name
     *
     * @param string $name
     * @return Varien_Simplexml_Object
     */
    public function getResourceTypeConfig($type)
    {
        return $this->_xml->global->resource->connection->types->{$type};
    }
}

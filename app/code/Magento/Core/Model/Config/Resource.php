<?php
/**
 * Resource configuration. Uses application configuration to retrieve resource connection information.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */ 
class Magento_Core_Model_Config_Resource
{
    const DEFAULT_READ_RESOURCE  = 'core_read';
    const DEFAULT_WRITE_RESOURCE = 'core_write';
    const DEFAULT_SETUP_RESOURCE = 'core_setup';

    protected $_data;

    /**
     * Retrieve resource connection instance name
     *
     * @param string $resourceName
     * @return string
     */
    public function getConnectionName($resourceName)
    {
        if (!isset($this->_data[$resourceName])) {
            $resourceName = (strpos($resourceName, 'read') !== false)
                ? self::DEFAULT_READ_RESOURCE
                : self::DEFAULT_WRITE_RESOURCE;
        }

        // TODO: Implement resource inheritance

        //return $this->_data[$resourceName]['connection'];
        return 'connection_default';
    }
}

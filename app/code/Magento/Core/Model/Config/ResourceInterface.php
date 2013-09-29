<?php
/**
 * Resource configuration interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Core_Model_Config_ResourceInterface
{
    /**
     * Retrieve resource connection instance name
     *
     * @param string $resourceName
     * @return string
     */
    public function getConnectionName($resourceName);
}

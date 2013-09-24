<?php
/**
 * Connection adapter interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Core_Model_Resource_ConnectionAdapterInterface
{
    /**
     * Get connection
     *
     * @return Magento_DB_Adapter_Interface|null
     */
    public function getConnection();
}
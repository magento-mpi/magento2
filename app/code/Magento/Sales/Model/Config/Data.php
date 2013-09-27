<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales configuration data container
 */
class Magento_Sales_Model_Config_Data extends Magento_Config_Data
{
    /**
     * @param Magento_Sales_Model_Config_Reader $reader
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Magento_Sales_Model_Config_Reader $reader,
        Magento_Config_CacheInterface $cache,
        $cacheId = 'sales_totals_config_cache'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }
}

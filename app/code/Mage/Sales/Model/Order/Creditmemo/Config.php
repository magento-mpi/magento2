<?php
/**
 * Order creditmemo configuration model
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Sales_Model_Order_Creditmemo_Config extends Mage_Sales_Model_Order_Total_Config_Base
{
    /**
     * Cache key for collectors
     *
     * @var string
     */
    protected $_collectorsCacheKey = 'sorted_order_creditmemo_collectors';

    /**
     * @param Mage_Core_Model_Cache_Type_Config $configCacheType
     * @param Mage_Core_Model_Config $config
     */
    public function __construct(
        Mage_Core_Model_Cache_Type_Config $configCacheType,
        Mage_Core_Model_Config $config
    ) {
        parent::__construct($configCacheType, $config->getNode('global/sales/order_creditmemo'));
    }
}

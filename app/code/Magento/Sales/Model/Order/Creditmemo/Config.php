<?php
/**
 * Order creditmemo configuration model
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Sales_Model_Order_Creditmemo_Config extends Magento_Sales_Model_Order_Total_Config_Base
{
    /**
     * Cache key for collectors
     *
     * @var string
     */
    protected $_collectorsCacheKey = 'sorted_order_creditmemo_collectors';

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Sales_Model_Order_TotalFactory $orderTotalFactory
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        Magento_Core_Model_Logger $logger,
        Magento_Sales_Model_Order_TotalFactory $orderTotalFactory,
        Magento_Core_Model_Config $coreConfig
    ) {
        parent::__construct(
            $configCacheType,
            $logger,
            $orderTotalFactory,
            $coreConfig->getNode('global/sales/order_creditmemo')
        );
        $this->_coreConfig = $coreConfig;
    }
}

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
 * Order invoice configuration model
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Order_Invoice_Config extends Magento_Sales_Model_Order_Total_Config_Base
{
    /**
     * Cache key for collectors
     *
     * @var string
     */
    protected $_collectorsCacheKey = 'sorted_order_invoice_collectors';

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     * @param Magento_Core_Model_Config $config
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        Magento_Core_Model_Config $config
    ) {
        parent::__construct($logger, $configCacheType, $config->getNode('global/sales/order_invoice'));
    }
}

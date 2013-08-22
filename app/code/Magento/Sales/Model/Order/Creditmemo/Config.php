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
 * Order creditmemo configuration model
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
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
     * Constructor
     *
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     */
    public function __construct(Magento_Core_Model_Cache_Type_Config $configCacheType)
    {
        parent::__construct($configCacheType, Mage::getConfig()->getNode('global/sales/order_creditmemo'));
    }
}

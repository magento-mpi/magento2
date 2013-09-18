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
namespace Magento\Sales\Model\Order\Invoice;

class Config extends \Magento\Sales\Model\Order\Total\Config\Base
{
    /**
     * Cache key for collectors
     *
     * @var string
     */
    protected $_collectorsCacheKey = 'sorted_order_invoice_collectors';

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        Magento_Core_Model_Config $coreConfig
    ) {
        $this->_coreConfig = $coreConfig;
        parent::__construct($configCacheType, $this->_coreConfig->getNode('global/sales/order_invoice'));
    }
}

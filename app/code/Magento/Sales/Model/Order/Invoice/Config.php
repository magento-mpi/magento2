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
     * @var \Magento\Core\Model\Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Sales\Model\Order\TotalFactory $orderTotalFactory
     * @param \Magento\Core\Model\Config $coreConfig
     */
    public function __construct(
        \Magento\Core\Model\Cache\Type\Config $configCacheType,
        \Magento\Core\Model\Logger $logger,
        \Magento\Sales\Model\Order\TotalFactory $orderTotalFactory,
        \Magento\Core\Model\Config $coreConfig
    ) {
        $this->_coreConfig = $coreConfig;
        parent::__construct(
            $configCacheType,
            $logger,
            $orderTotalFactory,
            $this->_coreConfig->getNode('global/sales/order_invoice')
        );
    }
}

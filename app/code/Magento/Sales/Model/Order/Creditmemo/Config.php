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
namespace Magento\Sales\Model\Order\Creditmemo;

class Config extends \Magento\Sales\Model\Order\Total\Config\Base
{
    /**
     * Cache key for collectors
     *
     * @var string
     */
    protected $_collectorsCacheKey = 'sorted_order_creditmemo_collectors';

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param Magento_Core_Model_Logger $logger
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     * @param \Magento\Core\Model\Config $coreConfig
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        \Magento\Core\Model\Cache\Type\Config $configCacheType,
        \Magento\Core\Model\Config $coreConfig
    ) {
        parent::__construct($logger, $configCacheType, $coreConfig->getNode('global/sales/order_creditmemo'));
        $this->_coreConfig = $coreConfig;
    }
}

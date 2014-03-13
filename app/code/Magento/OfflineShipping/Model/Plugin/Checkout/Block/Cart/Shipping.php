<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Checkout cart shipping block plugin
 *
 * @category   Magento
 * @package    Magento_OfflineShipping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\OfflineShipping\Model\Plugin\Checkout\Block\Cart;

class Shipping
{
    /**
     * @var \Magento\Store\Model\Store\Config
     */
    protected $_storeConfig;

    /**
     * @param \Magento\Store\Model\Store\Config $storeConfig
     */
    public function __construct(\Magento\Store\Model\Store\Config $storeConfig)
    {
        $this->_storeConfig = $storeConfig;
    }

    /**
     * @param \Magento\Checkout\Block\Cart\Shipping $subject
     * @param  bool $result
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetStateActive(\Magento\Checkout\Block\Cart\Shipping $subject, $result)
    {
        return (bool)$result || (bool)$this->_storeConfig->getConfig('carriers/tablerate/active');
    }
}

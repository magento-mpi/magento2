<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\Dhl\Model\Plugin\Checkout\Block\Cart;

/**
 * Checkout cart shipping block plugin
 */
class Shipping
{
    /**
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_storeConfig;

    /**
     * @param \Magento\Core\Model\Store\Config $storeConfig
     */
    public function __construct(\Magento\Core\Model\Store\Config $storeConfig)
    {
        $this->_storeConfig = $storeConfig;
    }

    /**
     * @param \Magento\Checkout\Block\Cart\Shipping $subject
     * @param bool $result
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetStateActive(\Magento\Checkout\Block\Cart\Shipping $subject, $result)
    {
        return (bool)$result || (bool)$this->_storeConfig->getConfig('carriers/dhl/active');
    }

    /**
     * @param \Magento\Checkout\Block\Cart\Shipping $subject
     * @param bool $result
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetCityActive(\Magento\Checkout\Block\Cart\Shipping $subject, $result)
    {
        return (bool)$result || (bool)$this->_storeConfig->getConfig('carriers/dhl/active');
    }
}
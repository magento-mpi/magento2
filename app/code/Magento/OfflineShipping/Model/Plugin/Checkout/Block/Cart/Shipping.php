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
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Magento\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Checkout\Block\Cart\Shipping $subject
     * @param  bool $result
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetStateActive(\Magento\Checkout\Block\Cart\Shipping $subject, $result)
    {
        return (bool)$result || (bool)$this->_scopeConfig->getValue('carriers/tablerate/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}

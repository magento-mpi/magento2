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
    protected $_storeConfig;

    /**
     * @param \Magento\App\Config\ScopeConfigInterface $storeConfig
     */
    public function __construct(\Magento\App\Config\ScopeConfigInterface $storeConfig)
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
        return (bool)$result || (bool)$this->_storeConfig->getValue('carriers/tablerate/active', \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
    }
}

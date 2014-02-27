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
     * @todo Uncomment first parameter after merge with mainline
     *
     * @param  bool $result
     * @return bool
     */
    public function afterGetStateActive(/*\Magento\Checkout\Block\Cart\Shipping $subject, */$result)
    {
        return (bool)$result || (bool)$this->_storeConfig->getConfig('carriers/tablerate/active');
    }
}

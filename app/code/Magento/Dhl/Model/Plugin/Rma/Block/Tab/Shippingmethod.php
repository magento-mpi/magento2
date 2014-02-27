<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\Dhl\Model\Plugin\Rma\Block\Tab;

/**
 * Checkout cart shipping block plugin
 */
class Shippingmethod
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
     * @param \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shippingmethod $subject
     * @param bool $result
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCanDisplayCustomValue(
        \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shippingmethod $subject,
        $result
    ) {
        return (bool)$result || (bool)$this->_storeConfig->getConfig('carriers/dhl/active');
    }
}
<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\Fedex\Model\Plugin\Rma\Block\Adminhtml\Rma\Edit\Tab\General;

/**
 * Checkout cart shipping block plugin
 */
class Shippingmethod
{
    /**
     * @var \Magento\Store\Model\Config
     */
    protected $_storeConfig;

    /**
     * @param \Magento\Store\Model\Config $storeConfig
     */
    public function __construct(\Magento\Store\Model\Config $storeConfig)
    {
        $this->_storeConfig = $storeConfig;
    }

    /**
     * @param \Magento\Object $subject
     * @param bool $result
     * @return bool
     */
    public function afterCanDisplayCustomValue(
        \Magento\Object $subject,
        $result
    ) {
        $carrierCode = $subject->getShipment()->getCarrierCode();
        if (!$carrierCode) {
            return (bool)$result || false;
        }
        return (bool)$result || (bool)$carrierCode == \Magento\Fedex\Model\Carrier::CODE;
    }
}
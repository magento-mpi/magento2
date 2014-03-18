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
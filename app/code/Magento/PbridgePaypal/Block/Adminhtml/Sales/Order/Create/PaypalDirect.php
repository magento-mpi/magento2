<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Paypal Direct payment block
 */
namespace Magento\PbridgePaypal\Block\Adminhtml\Sales\Order\Create;

class PaypalDirect extends \Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\AbstractCreate
{
    /**
     * Return 3D validation flag
     *
     * @return bool
     */
    public function is3dSecureEnabled()
    {
        return (bool)$this->getMethod()->getConfigData('centinel')
            && (bool)$this->getMethod()->getConfigData('centinel_backend');
    }
}

<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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

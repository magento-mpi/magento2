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
namespace Magento\PbridgePaypal\Block\Checkout\Payment;

class PaypalDirect extends \Magento\Pbridge\Block\Payment\Form\AbstractForm
{
    /**
     * Return 3D validation flag
     *
     * @return bool
     */
    public function is3dSecureEnabled()
    {
        return (bool)$this->getMethod()->getConfigData('centinel');
    }
}

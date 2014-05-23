<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pbridge\Block\Checkout\Payment;

class AbstractPayment extends \Magento\Pbridge\Block\Payment\Form\AbstractForm
{
    /**
     * Return 3D validation flag
     *
     * @return bool
     */
    public function is3dSecureEnabled()
    {
        if ($this->hasMethod() && $this->getMethod()->is3dSecureEnabled()) {
            return true;
        }
        return parent::is3dSecureEnabled();
    }
}

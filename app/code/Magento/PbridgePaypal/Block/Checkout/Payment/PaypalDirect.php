<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Paypal Direct payment block
 */
namespace Magento\PbridgePaypal\Block\Checkout\Payment;

class PaypalDirect extends \Magento\Pbridge\Block\Payment\Form\AbstractForm
{
    /**
     * Paypal payment code
     *
     * @var string
     */
    protected $_code = \Magento\Paypal\Model\Config::METHOD_WPP_DIRECT;

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

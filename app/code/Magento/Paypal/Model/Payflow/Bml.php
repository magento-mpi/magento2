<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Paypal\Model\Payflow;

class Bml extends \Magento\Paypal\Model\Express
{
    /**
     * Payment method code
     * @var string
     */
    protected $_code  = Config::METHOD_WPP_PE_BML;

    /**
     * Checkout payment form
     * @var string
     */
    protected $_formBlockType = 'Magento\Paypal\Block\Payflow\Bml\Form';

    /**
     * Checkout redirect URL getter for onepage checkout
     *
     * @return string
     */
    public function getCheckoutRedirectUrl()
    {
        return $this->_urlBuilder->getUrl('paypal/payflowbml/start');
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Paypal\Block\PayflowExpress;

class Form extends \Magento\Paypal\Block\Express\Form
{
    /**
     * Payment method code
     * @var string
     */
    protected $_methodCode = \Magento\Paypal\Model\Config::METHOD_WPP_PE_EXPRESS;

    /**
     * No billing agreements available for payflow express
     *
     * @return bool
     */
    public function getBillingAgreementcode()
    {
        return false;
    }
}

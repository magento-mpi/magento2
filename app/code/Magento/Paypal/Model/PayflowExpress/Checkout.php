<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wrapper that performs Paypal Express and Checkout communication
 * Use current Paypal Express method instance
 */
namespace Magento\Paypal\Model\PayflowExpress;

class Checkout extends \Magento\Paypal\Model\Express\Checkout
{
    /**
     * Api Model Type
     *
     * @var string
     */
    protected $_apiType = 'Magento\Paypal\Model\Api\PayflowNvp';

    /**
     * Payment method type
     *
     * @var string
     */
    protected $_methodType = \Magento\Paypal\Model\Config::METHOD_WPP_PE_EXPRESS;

    /**
     * Set shipping method to quote, if needed
     * @param string $methodCode
     */
    public function updateShippingMethod($methodCode)
    {
        parent::updateShippingMethod($methodCode);
        $this->_quote->save();
    }
}

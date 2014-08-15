<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Block\Onepage;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Fixture\AddressInjectable;
use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Billing
 * One page checkout status billing block
 */
class Billing extends Form
{
    /**
     * Continue checkout button
     *
     * @var string
     */
    protected $continue = '#billing-buttons-container button';

    /**
     * 'Ship to different address' radio button
     *
     * @var string
     */
    protected $useForShipping = '[id="billing:use_for_shipping_no"]';

    /**
     * Wait element
     *
     * @var string
     */
    protected $waitElement = '.loading-mask';

    /**
     * Fill billing address
     *
     * @param AddressInjectable $billingAddress
     * @param CustomerInjectable $customer
     * @param bool $diffShippingAddress
     * @return void
     */
    public function fillBilling(
        AddressInjectable $billingAddress = null,
        CustomerInjectable $customer = null,
        $diffShippingAddress = false
    ) {
        if ($billingAddress) {
            $this->fill($billingAddress);
        }
        if ($customer) {
            $this->fill($customer);
        }
        if ($diffShippingAddress) {
            $this->_rootElement->find($this->useForShipping)->click();
        }
    }

    /**
     * Click continue on billing information block
     *
     * @return void
     */
    public function clickContinue()
    {
        $this->_rootElement->find($this->continue)->click();
        $this->waitForElementNotVisible($this->waitElement);
    }
}

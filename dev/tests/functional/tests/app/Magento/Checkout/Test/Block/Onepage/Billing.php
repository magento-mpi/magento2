<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Block\Onepage;

use Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Fixture\AddressInjectable;
use Mtf\Block\Form;
use Mtf\Client\Element;

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
     * Locator for customer attribute on New Order page
     *
     * @var string
     */
    protected $customerAttribute = "[name='billing[%s]']";

    /**
     * Fill billing address
     *
     * @param AddressInjectable $billingAddress
     * @param CustomerInjectable $customer
     * @param bool $isShippingAddress
     * @return void
     */
    public function fillBilling(
        AddressInjectable $billingAddress = null,
        CustomerInjectable $customer = null,
        $isShippingAddress = false
    ) {
        if ($billingAddress) {
            $this->fill($billingAddress);
        }
        if ($customer) {
            $this->fill($customer);
        }
        if ($isShippingAddress) {
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

    /**
     * Check for visible customer attribute
     *
     * @param CustomerCustomAttribute $customerAttribute
     * @return bool
     */
    public function isCustomerAttributeVisible(CustomerCustomAttribute $customerAttribute)
    {
        return $this->_rootElement->find(
            sprintf($this->customerAttribute, $customerAttribute->getAttributeCode())
        )->isVisible();
    }
}

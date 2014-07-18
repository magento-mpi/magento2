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
use Magento\Checkout\Test\Fixture\Checkout;
use Mtf\Fixture\FixtureInterface;

/**
 * Class Billing
 * One page checkout status billing block
 *
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
     * @param Checkout $fixture
     * @return void
     */
    public function fillBilling(Checkout $fixture)
    {
        $billingAddress = $fixture->getBillingAddress();
        if ($billingAddress) {
            $this->fill($billingAddress);
        }
        if ($fixture->getShippingAddress()) {
            $this->_rootElement->find($this->useForShipping, Locator::SELECTOR_CSS)->click();
        }
        $this->clickContinue();
    }

    /**
     * Click continue on billing information block
     *
     * @return void
     */
    public function clickContinue()
    {
        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible($this->waitElement);
    }

    /**
     * Fill billing information
     *
     * @param CustomerInjectable $customer
     * @return void
     */
    public function fillBillingAddress(CustomerInjectable $customer)
    {
        $address = $customer->hasData('address')
            ? $customer->getDataFieldConfig('address')['source']->getAddress()
            : null;
        parent::fill($customer);
        if ($address instanceof AddressInjectable) {
            parent::fill($address);
        }
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Block\Onepage;

use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Checkout\Test\Fixture\Checkout;

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
     */
    public function clickContinue()
    {
        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible($this->waitElement);
    }

    /**
     * Check if Customer custom Attribute visible
     *
     * @param string $attributeCode
     * @return bool
     */
    public function isCustomerAttributeVisible($attributeCode)
    {
        $selector = "[name='billing[$attributeCode]']";
        return $this->_rootElement->find($selector)->isVisible();
    }
}

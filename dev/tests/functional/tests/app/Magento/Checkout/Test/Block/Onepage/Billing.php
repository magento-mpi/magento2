<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Block\Onepage;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class Billing
 * One page checkout status
 *
 * @package Magento\Checkout\Test\Block\Onepage
 */
class Billing extends Form
{
    /**
     * Continue checkout button
     *
     * @var string
     */
    private $continue;

    /**
     * 'Ship to different address' radio button
     *
     * @var string
     */
    private $useForShipping;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        $this->_mapping = array(
            'firstname' => '[id="billing:firstname"]',
            'lastname' => '[id="billing:lastname"]',
            'email' => '[id="billing:email"]',
            'telephone' => '[id="billing:telephone"]',
            'street_1' => '[id="billing:street1"]',
            'city' => '[id="billing:city"]',
            'region' => '[id="billing:region_id"]',
            'postcode' => '[id="billing:postcode"]',
            'country' => '[id="billing:country_id"]',
        );
        $this->continue = '#billing-buttons-container button';
        $this->useForShipping = '[id="billing:use_for_shipping_no"]';
    }

    /**
     * Fill billing address
     *
     * @param Checkout $fixture
     */
    public function fillBilling(Checkout $fixture)
    {
        $billingAddress = $fixture->getBillingAddress();
        $this->fill($billingAddress);
        if ($fixture->getShippingAddress()) {
            $this->_rootElement->find($this->useForShipping, Locator::SELECTOR_CSS)->click();
        }
        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible('.please-wait');
    }
}

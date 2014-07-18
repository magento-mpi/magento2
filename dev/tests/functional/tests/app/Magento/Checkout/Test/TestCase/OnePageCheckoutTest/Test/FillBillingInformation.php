<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestCase\OnePageCheckoutTest\Test;

use Magento\Checkout\Test\Page\CheckoutOnepage;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Mtf\TestCase\Step\StepInterface;

/**
 * Class FillBillingInformation
 * Fill billing information
 */
class FillBillingInformation implements StepInterface
{
    /**
     * Onepage checkout page
     *
     * @var CheckoutOnepage
     */
    protected $checkoutOnepage;

    /**
     * Customer fixture
     *
     * @var CustomerInjectable
     */
    protected $customer;

    /**
     * Preparing step properties
     *
     * @constructor
     * @param CheckoutOnepage $checkoutOnepage
     * @param CustomerInjectable $customer
     */
    public function __construct(
        CheckoutOnepage $checkoutOnepage,
        CustomerInjectable $customer
    ) {
        $this->checkoutOnepage = $checkoutOnepage;
        $this->customer = $customer;
    }

    /**
     * Run step that fill billing information
     *
     * @return void
     */
    public function run()
    {
        $this->checkoutOnepage->getBillingBlock()->fillBillingAddress($this->customer);
        $this->checkoutOnepage->getBillingBlock()->clickContinue();
    }
}

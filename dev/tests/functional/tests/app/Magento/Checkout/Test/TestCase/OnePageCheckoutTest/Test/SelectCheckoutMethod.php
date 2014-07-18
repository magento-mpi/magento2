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
 * Class SelectCheckoutMethod
 * Selecting checkout method
 */
class SelectCheckoutMethod implements StepInterface
{
    /**
     * Onepage checkout page
     *
     * @var CheckoutOnepage
     */
    protected $checkoutOnepage;

    /**
     * Checkout method
     *
     * @var string
     */
    protected $checkoutMethod;

    /*
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
     * @param string $checkoutMethod
     */
    public function __construct(CheckoutOnepage $checkoutOnepage, CustomerInjectable $customer, $checkoutMethod)
    {
        $this->checkoutOnepage = $checkoutOnepage;
        $this->checkoutMethod = $checkoutMethod;
        $this->customer = $customer;
    }

    /**
     * Run step that selecting checkout method
     *
     * @return void
     */
    public function run()
    {
        $checkoutMethodBlock = $this->checkoutOnepage->getLoginBlock();
        switch ($this->checkoutMethod) {
            case 'guest':
                $checkoutMethodBlock->guestCheckout();
                break;
            case 'register':
                $checkoutMethodBlock->registerCustomer();
                break;
            case 'login':
                $checkoutMethodBlock->loginAsCustomer($this->customer);
                break;
            default:
                break;
        }
    }
}

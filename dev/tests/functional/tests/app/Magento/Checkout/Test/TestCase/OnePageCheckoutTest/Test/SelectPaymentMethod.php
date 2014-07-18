<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestCase\OnePageCheckoutTest\Test;

use Magento\Checkout\Test\Page\CheckoutOnepage;
use Mtf\TestCase\Step\StepInterface;

/**
 * Class SelectPaymentMethod
 * Selecting payment method
 */
class SelectPaymentMethod implements StepInterface
{
    /**
     * Onepage checkout page
     *
     * @var CheckoutOnepage
     */
    protected $checkoutOnepage;

    /**
     * Payment method
     *
     * @var string
     */
    protected $paymentMethod;

    /**
     * Preparing step properties
     *
     * @constructor
     * @param CheckoutOnepage $checkoutOnepage
     * @param string $paymentMethod
     */
    public function __construct(CheckoutOnepage $checkoutOnepage, $paymentMethod)
    {
        $this->checkoutOnepage = $checkoutOnepage;
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * Run step that selecting payment method
     *
     * @return void
     */
    public function run()
    {
        $this->checkoutOnepage->getPaymentMethodsBlock()->clickOnPaymentMethod($this->paymentMethod);
        $this->checkoutOnepage->getPaymentMethodsBlock()->pressContinue();
    }
}

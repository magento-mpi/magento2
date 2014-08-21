<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerBalance\Test\TestStep;

use Magento\Checkout\Test\Page\CheckoutOnepage;
use Mtf\TestStep\TestStepInterface;

/**
 * Class SelectStoreCreditStep
 * Select store credit on onepage checkout page
 */
class SelectStoreCreditStep implements TestStepInterface
{
    /**
     * Array with payment methods
     *
     * @var array
     */
    protected $payment;

    /**
     * Onepage checkout page
     *
     * @var CheckoutOnepage
     */
    protected $checkoutOnepage;

    /**
     * @constructor
     * @param CheckoutOnepage $checkoutOnepage
     * @param array $payment
     */
    public function __construct(CheckoutOnepage $checkoutOnepage, array $payment)
    {
        $this->payment = $payment;
        $this->checkoutOnepage = $checkoutOnepage;
    }

    /**
     * Select store credit
     *
     * @return void
     */
    public function run()
    {
        if ($this->payment['use_customer_balance'] !== '-') {
            $this->checkoutOnepage->getStoreCreditBlock()->fillStoreCredit($this->payment);
        }
    }
}

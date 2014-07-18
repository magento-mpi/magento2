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
 * Class FillShippingMethod
 * Fill shipping information
 */
class FillShippingMethod implements StepInterface
{
    /**
     * Onepage checkout page
     *
     * @var CheckoutOnepage
     */
    protected $checkoutOnepage;

    /**
     * Shipping carrier and method
     *
     * @var array
     */
    protected $shipping;

    /**
     * Preparing step properties
     *
     * @constructor
     * @param CheckoutOnepage $checkoutOnepage
     * @param array $shipping
     */
    public function __construct(CheckoutOnepage $checkoutOnepage, array $shipping)
    {
        $this->checkoutOnepage = $checkoutOnepage;
        $this->shipping = $shipping;
    }

    /**
     * Run step that selecting shipping
     *
     * @return void
     */
    public function run()
    {
        if ($this->shipping['carrier'] != '-') {
            $this->checkoutOnepage->getShippingMethodBlock()->selectShipping($this->shipping);
        }
    }
}

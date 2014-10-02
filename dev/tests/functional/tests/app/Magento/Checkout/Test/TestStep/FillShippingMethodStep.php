<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestStep;

use Magento\Checkout\Test\Page\CheckoutOnepage;
use Mtf\TestStep\TestStepInterface;

/**
 * Class FillShippingMethodStep
 * Fill shipping information
 */
class FillShippingMethodStep implements TestStepInterface
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
     * Flag for gift options
     *
     * @var string|null
     */
    protected $giftOptions;

    /**
     * @constructor
     * @param CheckoutOnepage $checkoutOnepage
     * @param array $shipping
     * @param string|null $giftOptions
     */
    public function __construct(CheckoutOnepage $checkoutOnepage, array $shipping, $giftOptions = null)
    {
        $this->checkoutOnepage = $checkoutOnepage;
        $this->shipping = $shipping;
        $this->giftOptions = $giftOptions;
    }

    /**
     * Select shipping method
     *
     * @return void
     */
    public function run()
    {
        if ($this->shipping['shipping_service'] !== '-') {
            $this->checkoutOnepage->getShippingMethodBlock()->selectShippingMethod($this->shipping);
            if ($this->giftOptions === null) {
                $this->checkoutOnepage->getShippingMethodBlock()->clickContinue();
            }
        }
    }
}

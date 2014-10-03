<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\TestStep;

use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\SalesRule\Test\Fixture\SalesRuleInjectable;
use Mtf\TestStep\TestStepInterface;

/**
 * Class ApplySalesRuleOnFrontendStep
 * Apply Sales Rule before one page checkout
 */
class ApplySalesRuleOnFrontendStep implements TestStepInterface
{
    /**
     * Checkout cart page
     *
     * @var CheckoutCart
     */
    protected $checkoutCart;

    /**
     * SalesRule fixture
     *
     * @var SalesRuleInjectable
     */
    protected $salesRule;

    /**
     * @constructor
     * @param CheckoutCart $checkoutCart
     * @param SalesRuleInjectable $salesRule
     */
    public function __construct(CheckoutCart $checkoutCart, SalesRuleInjectable $salesRule = null)
    {
        $this->checkoutCart = $checkoutCart;
        $this->salesRule = $salesRule;
    }

    /**
     * Apply gift card before one page checkout
     *
     * @return void
     */
    public function run()
    {
        if ($this->salesRule !== null) {
            $this->checkoutCart->getDiscountCodesBlock()->applyCouponCode($this->salesRule->getCouponCode());
        }
    }
}

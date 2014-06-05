<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Constraint;

/**
 * Class AssertCartPriceRuleFreeShippingIsApplied
 * Check that shopping cart free shipping is applied
 */
class AssertCartPriceRuleFreeShippingIsApplied extends AssertCartPriceRuleApplying
{
    const FREE_SHIPPING_PRICE = '0.00';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that free shipping is applied in shopping cart
     *
     * @return void
     */
    protected function assert()
    {
        preg_match('/\$(.*)$/', $this->checkoutCart->getTotalsBlock()->getChippingPrice(), $shippingPriceMatch);
        $currentShippingPrice = $shippingPriceMatch[1];

        \PHPUnit_Framework_Assert::assertEquals(
            $currentShippingPrice,
            self::FREE_SHIPPING_PRICE,
            'Current shipping price: \'' . $this->checkoutCart->getTotalsBlock()->getChippingPrice()
            . '\' not equals with free shipping price: \'' . self::FREE_SHIPPING_PRICE . '\''
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Free shipping is applied.';
    }
}

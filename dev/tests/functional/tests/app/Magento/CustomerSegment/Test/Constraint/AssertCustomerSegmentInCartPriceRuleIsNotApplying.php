<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Constraint;

/**
 * Class AssertCustomerSegmentInCartPriceRuleIsNotApplying
 * Assert that Catalog Price Rule is not applied on product(s) in shopping cart according to rule condition
 */
class AssertCustomerSegmentInCartPriceRuleIsNotApplying extends AssertCustomerSegmentPriceRuleApplying
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that shopping cart subtotal equals with grand total
     *
     * @return void
     */
    protected function assert()
    {
        $subTotal = $this->checkoutCart->getTotalsBlock()->getSubtotal();
        $grandTotal = $this->checkoutCart->getTotalsBlock()->getGrandTotal();

        if ($this->checkoutCart->getTotalsBlock()->isVisibleShippingPriceBlock()) {
            $shippingPrice = $this->checkoutCart->getTotalsBlock()->getShippingPrice();
            $grandTotal = number_format(($grandTotal - $shippingPrice), 2);
        }

        \PHPUnit_Framework_Assert::assertEquals(
            $subTotal,
            $grandTotal,
            'Price is not equals'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Catalog Price Rule is applied on product in shopping cart.';
    }
}

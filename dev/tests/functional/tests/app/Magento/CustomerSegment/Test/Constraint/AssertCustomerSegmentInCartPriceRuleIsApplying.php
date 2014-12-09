<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Constraint;

/**
 * Class AssertCustomerSegmentInCartPriceRule
 * Assert that shopping cart subtotal not equals with grand total
 */
class AssertCustomerSegmentInCartPriceRuleIsApplying extends AbstractAssertCustomerSegmentPriceRuleApplying
{
    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Assert that Catalog Price Rule is applied on product(s) in shopping cart according to rule condition has been
     * applied in shopping cart page
     *
     * @return void
     */
    protected function assert()
    {
        $totalBlock = $this->checkoutCart->getTotalsBlock();
        $grandTotal = $totalBlock->getGrandTotal();

        if ($totalBlock->isVisibleShippingPriceBlock()) {
            $shippingPrice = $totalBlock->getShippingPrice();
            $grandTotal = number_format(($grandTotal - $shippingPrice), 2);
        }

        \PHPUnit_Framework_Assert::assertNotEquals(
            $totalBlock->getSubtotal(),
            $grandTotal,
            'Catalog Price Rule is not applied on product in shopping cart.'
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

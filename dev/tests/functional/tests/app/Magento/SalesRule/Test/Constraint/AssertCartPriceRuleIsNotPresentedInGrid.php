<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\SalesRule\Test\Fixture\SalesRuleInjectable;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteIndex;

/**
 * Class AssertCartPriceRuleIsNotPresentedInGrid
 */
class AssertCartPriceRuleIsNotPresentedInGrid extends AbstractConstraint
{
    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Assert that sales rule is not present in cart price rules grid
     *
     * @param PromoQuoteIndex $promoQuoteIndex
     * @param SalesRuleInjectable $salesRule
     * @return void
     */
    public function processAssert(PromoQuoteIndex $promoQuoteIndex, SalesRuleInjectable $salesRule)
    {
        $filter = [
            'name' => $salesRule->getName(),
        ];
        \PHPUnit_Framework_Assert::assertFalse(
            $promoQuoteIndex->getPromoQuoteGrid()->isRowVisible($filter),
            'Sales rule \'' . $salesRule->getName() . '\' is present in cart price rules grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Sales rule is not present in cart price rules grid.';
    }
}

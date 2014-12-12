<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\SalesRule\Test\Constraint;

use Magento\SalesRule\Test\Fixture\SalesRuleInjectable;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCartPriceRuleIsNotPresentedInGrid
 */
class AssertCartPriceRuleIsNotPresentedInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

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

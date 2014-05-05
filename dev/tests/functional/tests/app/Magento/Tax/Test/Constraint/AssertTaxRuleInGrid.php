<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Constraint;

use Magento\Tax\Test\Fixture\TaxRule;
use Magento\Tax\Test\Page\Adminhtml\TaxRuleIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertTaxRuleInGrid
 *
 * @package Magento\Tax\Test\Constraint
 */
class AssertTaxRuleInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert tax rule availability in Tax Rule grid
     *
     * @param TaxRule $taxRule
     * @param TaxRuleIndex $taxRuleIndex
     */
    public function processAssert(
        TaxRule $taxRule,
        TaxRuleIndex $taxRuleIndex
    ) {
        $filter = [
            'code' => $taxRule->getCode(),
        ];
        $taxRuleIndex->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $taxRuleIndex->getTaxRuleGrid()->isRowVisible($filter),
            'Tax Rule \'' . $taxRule->getCode() . '\' is absent in Tax Rule grid.'
        );
    }

    /**
     * Text of Tax Rule in grid assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Tax rule is present in grid.';
    }
}

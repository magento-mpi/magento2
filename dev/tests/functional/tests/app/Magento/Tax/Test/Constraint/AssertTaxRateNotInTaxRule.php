<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Constraint;

use Magento\Tax\Test\Fixture\TaxRate;
use Magento\Tax\Test\Fixture\TaxRule;
use Magento\Tax\Test\Page\Adminhtml\TaxRuleIndex;
use Magento\Tax\Test\Page\Adminhtml\TaxRuleNew;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertTaxRateNotInTaxRule
 */
class AssertTaxRateNotInTaxRule extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that tax rate is absent in tax rule form
     *
     * @param TaxRate $taxRate
     * @param TaxRule $taxRule
     * @param TaxRuleIndex $taxRuleIndex
     * @param TaxRuleNew $taxRuleNew
     * @return void
     */
    public function processAssert(
        TaxRate $taxRate,
        TaxRule $taxRule,
        TaxRuleIndex $taxRuleIndex,
        TaxRuleNew $taxRuleNew
    ) {
        $filter = [
            'code' => $taxRule->getCode(),
        ];

        $taxRuleIndex->open();
        $taxRuleIndex->getTaxRuleGrid()->searchAndOpen($filter);
        $taxRatesList = $taxRuleNew->getTaxRuleForm()->getAllTaxRates();
        \PHPUnit_Framework_Assert::assertFalse(
            in_array($taxRate->getCode(), $taxRatesList),
            'Tax Rate \'' . $filter['code'] . '\' is present in Tax Rule form.'
        );
    }

    /**
     * Text of Tax Rate not in Tax Rule form
     *
     * @return string
     */
    public function toString()
    {
        return 'Tax rate is absent in tax rule from.';
    }
}

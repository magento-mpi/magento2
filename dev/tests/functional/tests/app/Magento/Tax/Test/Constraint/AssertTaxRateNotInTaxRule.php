<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Tax\Test\Constraint;

use Magento\Tax\Test\Fixture\TaxRate;
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
     * @param TaxRuleNew $taxRuleNew
     * @return void
     */
    public function processAssert(
        TaxRate $taxRate,
        TaxRuleNew $taxRuleNew
    ) {
        $taxRuleNew->open();
        $taxRatesList = $taxRuleNew->getTaxRuleForm()->getAllTaxRates();
        \PHPUnit_Framework_Assert::assertFalse(
            in_array($taxRate->getCode(), $taxRatesList),
            'Tax Rate \'' . $taxRate->getCode() . '\' is present in Tax Rule form.'
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

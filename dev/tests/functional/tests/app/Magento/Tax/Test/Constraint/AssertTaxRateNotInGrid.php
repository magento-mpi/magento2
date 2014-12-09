<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Tax\Test\Page\Adminhtml\TaxRateIndex;
use Magento\Tax\Test\Fixture\TaxRate;

/**
 * Class AssertTaxRateNotInGrid
 */
class AssertTaxRateNotInGrid extends AbstractConstraint
{
    /* tags */
     const SEVERITY = 'high';
     /* end tags */

    /**
     * Assert that tax rate not available in Tax Rate grid
     *
     * @param TaxRateIndex $taxRateIndex
     * @param TaxRate $taxRate
     * @return void
     */
    public function processAssert(
        TaxRateIndex $taxRateIndex,
        TaxRate $taxRate
    ) {
        $filter = [
            'code' => $taxRate->getCode(),
        ];

        $taxRateIndex->open();
        \PHPUnit_Framework_Assert::assertFalse(
            $taxRateIndex->getTaxRateGrid()->isRowVisible($filter),
            'Tax Rate \'' . $filter['code'] . '\' is present in Tax Rate grid.'
        );
    }

    /**
     * Text of Tax Rate not in grid assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Tax rate is absent in grid.';
    }
}

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
 * Class AssertTaxRateInGrid
 */
class AssertTaxRateInGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Assert tax rule availability in Tax Rate grid
     *
     * @param TaxRateIndex $taxRateIndexPage
     * @param TaxRate $taxRate
     * @param TaxRate $initialTaxRate
     * @return void
     */
    public function processAssert(
        TaxRateIndex $taxRateIndexPage,
        TaxRate $taxRate,
        TaxRate $initialTaxRate = null
    ) {
        $data = ($initialTaxRate === null)
            ? $taxRate->getData()
            : array_merge($initialTaxRate->getData(), $taxRate->getData());
        $filter = [
            'code' => $data['code'],
            'tax_country_id' => $data['tax_country_id'],
        ];
        $filter['tax_postcode'] = ($data['zip_is_range'] === 'No')
            ? $data['tax_postcode']
            : $data['zip_from'] . '-' . $data['zip_to'];

        $taxRateIndexPage->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $taxRateIndexPage->getTaxRateGrid()->isRowVisible($filter),
            'Tax Rate \'' . $filter['code'] . '\' is absent in Tax Rate grid.'
        );
    }

    /**
     * Text of Tax Rate in grid assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Tax rate is present in grid.';
    }
}

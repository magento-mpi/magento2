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
use Magento\Tax\Test\Page\Adminhtml\TaxRateNew;
use Magento\Tax\Test\Fixture\TaxRate;

/**
 * Class AssertTaxRateForm
 */
class AssertTaxRateForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that tax rate form filled right
     *
     * @param TaxRateIndex $taxRateIndex
     * @param TaxRateNew $taxRateNew
     * @param TaxRate $taxRate
     * @param TaxRate $initialTaxRate
     * @return void
     */
    public function processAssert(
        TaxRateIndex $taxRateIndex,
        TaxRateNew $taxRateNew,
        TaxRate $taxRate,
        TaxRate $initialTaxRate = null
    ) {
        $data = $taxRate->getData();
        $data['zip_is_range'] = ($data['zip_is_range'] === 'Yes') ? true : false;
        $data['rate'] = number_format($data['rate'], 4);
        if ($initialTaxRate !== null) {
            $taxRateCode = ($taxRate->hasData('code')) ? $taxRate->getCode() : $initialTaxRate->getCode();
        } else {
            $taxRateCode = $taxRate->getCode();
        }
        $filter = [
            'code' => $taxRateCode,
        ];

        $taxRateIndex->open();
        $taxRateIndex->getTaxRateGrid()->searchAndOpen($filter);
        $formData = $taxRateNew->getTaxRateForm()->getData($taxRate);
        $dataDiff = $this->verifyForm($formData, $data);
        \PHPUnit_Framework_Assert::assertTrue(
            empty($dataDiff),
            'Tax Rate form was filled not right.'
            . "\nLog:\n" . implode(";\n", $dataDiff)
        );
    }

    /**
     * Verifying that form is filled right
     *
     * @param array $formData
     * @param array $fixtureData
     * @return array $errorMessages
     */
    protected function verifyForm(array $formData, array $fixtureData)
    {
        $errorMessages = [];

        foreach ($fixtureData as $key => $value) {
            if ($value !== $formData[$key]) {
                $errorMessages[] = "Data in " . $key . " field not equal."
                    . "\nExpected: " . $value
                    . "\nActual: " . $formData[$key];
            }
        }

        return $errorMessages;
    }

    /**
     * Text that form was filled right
     *
     * @return string
     */
    public function toString()
    {
        return 'Tax Rate form has been filled right.';
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteIndex;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteEdit;
use Magento\SalesRule\Test\Fixture\SalesRuleInjectable;

/**
 * Class AssertCartPriceRuleForm
 */
class AssertCartPriceRuleForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Skipped fields for verify data
     *
     * @var array
     */
    protected $skippedFields = [
        'conditions_serialized',
        'actions_serialized'
    ];

    /**
     * Assert that displayed sales rule data on edit page(backend) equals passed from fixture
     *
     * @param PromoQuoteIndex $promoQuoteIndex
     * @param PromoQuoteEdit $promoQuoteEdit
     * @param SalesRuleInjectable $salesRule
     * @return void
     */
    public function processAssert(
        PromoQuoteIndex $promoQuoteIndex,
        PromoQuoteEdit $promoQuoteEdit,
        SalesRuleInjectable $salesRule
    ) {
        $filter = [
            'name' => $salesRule->getName(),
        ];
        $promoQuoteIndex->open();
        $promoQuoteIndex->getPromoQuoteGrid()->searchAndOpen($filter);
        $formData = $promoQuoteEdit->getSalesRuleForm()->getData($salesRule);
        $fixtureData = $salesRule->getData();
        $dataDiff = $this->verify($formData, $fixtureData);
        \PHPUnit_Framework_Assert::assertTrue(
            empty($dataDiff),
            'Sales rule data on edit page(backend) not equals to passed from fixture.'
                . "\nFailed values:\n " . implode(";\n ", $dataDiff)
        );
    }

    /**
     * Verify data in form equals to passed from fixture
     *
     * @param array $fixtureData
     * @param array $formData
     * @return array
     */
    protected function verify(array $formData, array $fixtureData)
    {
        $errorMessage = [];

        foreach ($fixtureData as $key => $value) {
            if (is_array($value)) {
                $diff = array_diff($value, $formData[$key]);
                $diff = array_merge($diff, array_diff($formData[$key], $value));
                if (!empty($diff)) {
                    $errorMessage[] = "Data in " . $key . " field not equal."
                        . "\nExpected: " . implode(", ", $value)
                        . "\nActual: " . implode(", ", $formData[$key]);
                }
            } else {
                if ($value !== $formData[$key] && !in_array($key, $this->skippedFields)) {
                    $errorMessage[] = "Data in " . $key . " field not equal."
                        . "\nExpected: " . $value
                        . "\nActual: " . $formData[$key];
                }
            }
        }

        return $errorMessage;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Displayed sales rule data on edit page(backend) equals to passed from fixture.';
    }
}

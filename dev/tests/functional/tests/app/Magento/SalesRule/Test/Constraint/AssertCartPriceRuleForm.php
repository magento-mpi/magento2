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
        'actions_serialized',
        'customer_group_ids',
        'website_ids'
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
        $dataForm = $promoQuoteEdit->getSalesRuleForm()->getData($salesRule);
        $dataFixture = $salesRule->getData();
        $dataDiff = $this->verify($dataFixture, $dataForm);
        \PHPUnit_Framework_Assert::assertTrue(
            empty($dataDiff),
            'Sales rule data on edit page(backend) not equals to passed from fixture.'
                . "\nFailed values: " . implode(', ', $dataDiff)
        );
    }

    /**
     * Verify data in form equals to passed from fixture
     *
     * @param array $dataFixture
     * @param array $dataForm
     * @return array
     */
    protected function verify(array $dataFixture, array $dataForm)
    {
        $result = [];

        $diff = $this->arrayDiffRecursive($dataFixture, $dataForm);
        foreach ($diff as $name => $value) {
            if (!in_array($name, $this->skippedFields)) {
                $result[] = $name . ' : ' . $value;
            }
        }

        return $result;
    }

    /**
     * Recursively compare two arrays by difference
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    protected function arrayDiffRecursive($array1, $array2)
    {
        $diff = [];
        foreach ($array1 as $array1Key => $array1Value) {
            if (array_key_exists($array1Key, $array2)) {
                if (is_array($array1Value)) {
                    $recursiveDiff = $this->arrayDiffRecursive($array1Value, $array2[$array1Key]);
                    if (count($recursiveDiff)) {
                        $diff[$array1Key] = $recursiveDiff;
                    }
                } else {
                    if ($array1Value != $array2[$array1Key]) {
                        $diff[$array1Key] = $array1Value;
                    }
                }
            } else {
                $diff[$array1Key] = $array1Value;
            }
        }
        return $diff;
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

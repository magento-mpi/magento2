<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\CatalogRule\Test\Fixture\CatalogRule;
use Magento\CatalogRule\Test\Page\Adminhtml\CatalogRuleIndex;
use Magento\CatalogRule\Test\Page\Adminhtml\CatalogRuleNew;

/**
 * Class AssertCatalogPriceRuleForm
 */
class AssertCatalogPriceRuleForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that displayed Catalog Price Rule data on edit page equals passed from fixture.
     *
     * @param CatalogRule $catalogPriceRule
     * @param CatalogRuleIndex $pageCatalogRuleIndex
     * @param CatalogRuleNew $pageCatalogRuleNew
     * @param CatalogRule $catalogPriceRuleOriginal
     */
    public function processAssert(
        CatalogRule $catalogPriceRule,
        CatalogRuleIndex $pageCatalogRuleIndex,
        CatalogRuleNew $pageCatalogRuleNew,
        CatalogRule $catalogPriceRuleOriginal = null
    ) {
        $data = $catalogPriceRule->getData();
        if ($catalogPriceRuleOriginal !== null) {
            $data['rule_id'] = (!isset($data['rule_id'])) ? $catalogPriceRuleOriginal->getId() : $data['rule_id'];
            $data['name'] = (!isset($data['name'])) ? $catalogPriceRuleOriginal->getName() : $data['name'];
            $filter = [
                'rule_id' => $data['rule_id'],
                'name' => $data['name'],
            ];
        } else {
            $filter = [
                'name' => $data['name'],
            ];
        }

        $pageCatalogRuleIndex->open();
        $pageCatalogRuleIndex->getCatalogRuleGrid()->searchAndOpen($filter);
        $formData = $pageCatalogRuleNew->getEditForm()->getData($catalogPriceRule);
        $fixtureData = $catalogPriceRule->getData();
        //convert discount_amount to float to compare
        if (isset($formData['discount_amount'])) {
            $formData['discount_amount'] = floatval($formData['discount_amount']);
        }
        if (isset($fixtureData['discount_amount'])) {
            $fixtureData['discount_amount'] = floatval($fixtureData['discount_amount']);
        }
        $diff = $this->verifyData($formData, $fixtureData);
        \PHPUnit_Framework_Assert::assertTrue(
            empty($diff),
            implode(' ', $diff)
        );
    }

    /**
     * Check if arrays have equal values
     *
     * @param array $formData
     * @param array $fixtureData
     * @return array
     */
    protected function verifyData(array $formData, array $fixtureData)
    {
        $errorMessage = [];
        foreach ($fixtureData as $key => $value) {
            if ($key == 'condition') {
                continue;
            }
            if (is_array($value)) {
                $diff = array_diff($value, $formData[$key]);
                $diff = array_merge($diff, array_diff($formData[$key], $value));
                if (!empty($diff)) {
                    $errorMessage[] = "Data in " . $key . " field not equal."
                        . "\nExpected: " . implode(", ", $value)
                        . "\nActual: " . implode(", ", $formData[$key]);
                }
            } else {
                if ($value !== $formData[$key]) {
                    $errorMessage[] = "Data in " . $key . " field not equal."
                        . "\nExpected: " . $value
                        . "\nActual: " . $formData[$key];
                }
            }
        }
        return $errorMessage;
    }

    /**
     * Text success verify Catalog Price Rule
     *
     * @return string
     */
    public function toString()
    {
        return 'Displayed catalog price rule data on edit page(backend) equals to passed from fixture.';
    }
}

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
     * @return void
     */
    public function processAssert(
        CatalogRule $catalogPriceRule,
        CatalogRuleIndex $pageCatalogRuleIndex,
        CatalogRuleNew $pageCatalogRuleNew
    ) {
        $rule_website = $catalogPriceRule->getWebsiteIds();
        $rule_website = reset($rule_website);
        $filter = [
            'name' => $catalogPriceRule->getName(),
            'is_active' => $catalogPriceRule->getIsActive(),
            'rule_website' => $rule_website,
        ];

        $pageCatalogRuleIndex->open();
        $pageCatalogRuleIndex->getCatalogRuleGrid()->searchAndOpen($filter);
        //convert discount_amount to float to compare
        $formData = $pageCatalogRuleNew->getEditForm()->getData($catalogPriceRule);
        $fixtureData = $catalogPriceRule->getData();
        $formData['discount_amount'] = floatval($formData['discount_amount']);
        $fixtureData['discount_amount'] = floatval($fixtureData['discount_amount']);

        \PHPUnit_Framework_Assert::assertTrue(
            $this->checkIfArraysEqualByValue(
                $formData,
                $fixtureData
            ),
            'Catalog Price Rule data on edit page(backend) not equals to passed from fixture.'
        );
    }

    /**
     * Check if arrays have equal values
     *
     * @param array $formData
     * @param array $fixtureData
     * @return bool
     */
    protected function checkIfArraysEqualByValue(array $formData, array $fixtureData)
    {
        foreach ($fixtureData as $key => $value) {
            if (is_array($value)) {
                $diff = array_diff($value, $formData[$key]);
                if (!empty($diff)) {
                    return false;
                }
            } else {
                if ($value !== $formData[$key]) {
                    return false;
                }
            }
        }
        return true;
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

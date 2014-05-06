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
 *
 * @package Magento\CatalogRule\Test\Constraint
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
        \PHPUnit_Framework_Assert::assertTrue(
            $this->checkIfArraysEqualByValue(
                $pageCatalogRuleNew->getEditForm()->getData($catalogPriceRule),
                $catalogPriceRule->getData()
            ),
            'Catalog Price Rule data on edit page(backend) not equals to passed from fixture.'
        );
    }

    /**
     * Sort array by keys
     *
     * @param $arr
     */
    public function sortArrayByKeys(&$arr) {
        ksort($arr);
        foreach ($arr as $key => &$value) {
            if (is_array($value)) {
                $this->sortArrayByKeys($value);
            }
        }
    }

    /**
     * Convert associative array to numeric
     *
     * @param $arr
     * @return array
     */
    public function convertAssocArrayToNumeric($arr) {
        $arr = array_values($arr);
        foreach ($arr as $key => &$value) {
            if (is_array($value)) {
                $value = array_values($value);
            }
        }
        return $arr;
    }

    /**
     * Compare 2 entities if they are equal
     *
     * @param $entity1
     * @param $entity2
     * @return bool
     */
    public function compareEntities($entity1, $entity2) {
        if (is_numeric($entity1)) {
            $entity1 = floatval($entity1);
        }
        if (is_numeric($entity2)) {
            $entity2 = floatval($entity2);
        }
        if (gettype($entity1) != gettype($entity2)) {
            return false;
        }
        if (is_array($entity1)) {
            if (count($entity1) != count($entity2)) {
                return false;
            }
            for ($i = 0; $i < count($entity1); $i++) {
                if (!$this->compareEntities($entity1[$i], $entity2[$i])) {
                    return false;
                }
            }
        } else if (is_numeric($entity1)) {
            if (abs($entity1 - $entity2) >= 0.000001) {
                return false;
            }
        } else if ($entity1 != $entity2) {
            return false;
        }
        return true;
    }

    /**
     * Check if arrays have equal values
     *
     * @param $arr1
     * @param $arr2
     * @return mixed
     */
    public function checkIfArraysEqualByValue($arr1, $arr2) {
        $this->sortArrayByKeys($arr1);
        $arr1 = $this->convertAssocArrayToNumeric($arr1);
        $this->sortArrayByKeys($arr2);
        $arr2 = $this->convertAssocArrayToNumeric($arr2);
        return $this->compareEntities($arr1, $arr2);
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

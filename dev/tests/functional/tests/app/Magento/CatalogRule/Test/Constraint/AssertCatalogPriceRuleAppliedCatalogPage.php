<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Class AssertCatalogPriceRuleAppliedCatalogPage
 */
class AssertCatalogPriceRuleAppliedCatalogPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that Catalog Price Rule is applied for product(s) in Catalog
     * according to Priority(Priority/Stop Further Rules Processing)
     *
     * @param CatalogProductSimple $product
     * @param CmsIndex $cmsIndex
     * @param CatalogCategoryView $catalogCategoryView
     * @param $price
     * @return void
     */
    public function processAssert(
        CatalogProductSimple $product,
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        array $price
    ) {
        $cmsIndex->open();
        $categoryName = $product->getCategoryIds()[0]['name'];
        $productName = $product->getName();
        $cmsIndex->getTopmenu()->selectCategoryByName($categoryName);
        $productPriceBlock = $catalogCategoryView->getListProductBlock()->getProductPriceBlock($productName);
        $actualPrice['sub_total'] = $productPriceBlock->getRegularPrice();
        $actualPrice['grand_total'] = $productPriceBlock->getSpecialPrice();
        $actualPrice['discount_amount'] = $actualPrice['sub_total'] - $actualPrice['grand_total'];
        $diff = $this->verifyData($actualPrice, $price);
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
            if ($value != $formData[$key]) {
                $errorMessage[] = "Data in " . $key . " field not equal."
                    . "\nExpected: " . $value
                    . "\nActual: " . $formData[$key];
            }
        }
        return $errorMessage;
    }

    /**
     * Text of catalog price rule visibility on catalog page (frontend)
     *
     * @return string
     */
    public function toString()
    {
        return 'Displayed catalog price rule data on catalog page(frontend) equals to passed from fixture.';
    }
}

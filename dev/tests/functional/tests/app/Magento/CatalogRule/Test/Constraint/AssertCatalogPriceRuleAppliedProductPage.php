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
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Class AssertCatalogPriceRuleAppliedProductPage
 */
class AssertCatalogPriceRuleAppliedProductPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that Catalog Price Rule is applied & it impacts on product's discount price on Product page
     *
     * @param CatalogProductSimple $product
     * @param CatalogProductView $pageCatalogProductView
     * @param CmsIndex $cmsIndex
     * @param CatalogCategoryView $catalogCategoryView
     * @param array $price
     * @return void
     */
    public function processAssert(
        CatalogProductSimple $product,
        CatalogProductView $pageCatalogProductView,
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        array $price
    ) {
        $cmsIndex->open();
        $categoryName = $product->getCategoryIds()[0]['name'];
        $productName = $product->getName();
        $cmsIndex->getTopmenu()->selectCategoryByName($categoryName);
        $catalogCategoryView->getListProductBlock()->openProductViewPage($productName);
        $productPriceBlock = $pageCatalogProductView->getViewBlock()->getProductPriceBlock();
        $actualPrice['regular'] = $productPriceBlock->getRegularPrice();
        $actualPrice['special'] = $productPriceBlock->getSpecialPrice();
        $actualPrice['discount_amount'] = $actualPrice['regular'] - $actualPrice['special'];
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
        foreach ($formData as $key => $value) {
            if ($value != $fixtureData[$key]) {
                $errorMessage[] = "Data not equal."
                    . "\nExpected: " . $fixtureData[$key]
                    . "\nActual: " . $value;
            }
        }
        return $errorMessage;
    }

    /**
     * Text of catalog price rule visibility on product page (frontend)
     *
     * @return string
     */
    public function toString()
    {
        return 'Displayed catalog price rule data on product page(frontend) equals to passed from fixture.';
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Class AssertNoUpSellsProductsSection
 */
class AssertNoUpSellsProductsSection extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that product is not displayed in up-sell section
     *
     * @param CatalogProductSimple $product1
     * @param CatalogProductSimple $product2
     * @param CmsIndex $cmsIndex
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogProductView $catalogProductView
     * @return void
     */
    public function processAssert(
        CatalogProductSimple $product1,
        CatalogProductSimple $product2,
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        CatalogProductView $catalogProductView
    ) {
        $categoryName = $product1->getCategoryIds()[0];
        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategoryByName($categoryName);
        $catalogCategoryView->getListProductBlock()->openProductViewPage($product1->getName());

        \PHPUnit_Framework_Assert::assertFalse(
            $catalogProductView->getUpsellBlock()->isUpsellProductVisible($product2->getName()),
            'Product \'' . $product2->getName() . '\' is exist in up-sells products.'
        );
    }

    /**
     * Text success product is not displayed in up-sell section
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is not displayed in up-sell section.';
    }
}

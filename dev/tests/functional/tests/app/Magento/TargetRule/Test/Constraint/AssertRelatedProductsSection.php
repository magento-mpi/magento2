<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Class AssertRelatedProductsSection
 */
class AssertRelatedProductsSection extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that product is displayed in related products section
     *
     * @param CatalogProductSimple $product1
     * @param CatalogProductSimple $product2
     * @param CmsIndex $cmsIndex
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogProductView $catalogProductView
     */
    public function processAssert(
        CatalogProductSimple $product1,
        CatalogProductSimple $product2,
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        CatalogProductView $catalogProductView
    ) {
        $categoryIds = $product1->getCategoryIds();
        $category = reset($categoryIds);

        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategoryByName($category['name']);
        $catalogCategoryView->getListProductBlock()->openProductViewPage($product1->getName());

        \PHPUnit_Framework_Assert::assertTrue(
            $catalogProductView->getRelatedProductBlock()->isRelatedProductVisible($product2->getName()),
            'Product \'' . $product2->getName() . '\' is absent in related products.'
        );
    }

    /**
     * Text success product is displayed in related products section
     *
     * @return string
     */
    public function toString()
    {
        return 'Assert that product is displayed in related products section.';
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Catalog\Test\Fixture\Category;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertProductVisibleInCategory
 *
 * @package Magento\Catalog\Test\Constraint
 */
class AssertProductVisibleInCategory extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that product is visible in the assigned category
     *
     * @param CatalogCategoryView $catalogCategoryView
     * @param CmsIndex $cmsIndex
     * @param CatalogProductSimple $product
     * @param Category $category
     * @return void
     */
    public function processAssert(
        CatalogCategoryView $catalogCategoryView,
        CmsIndex $cmsIndex,
        CatalogProductSimple $product,
        Category $category
    ) {
        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategoryByName($category->getCategoryName());
        \PHPUnit_Framework_Assert::assertTrue(
            $catalogCategoryView->getListProductBlock()->isProductVisible($product->getName()),
            'Product is absent on category page.'
        );
    }

    /**
     * Text of Visible in category assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is visible in the assigned category.';
    }
}

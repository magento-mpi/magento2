<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\Category;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Class AssertProductVisibleInCategory
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
     * @param FixtureInterface $product
     * @param Category $category
     * @return void
     */
    public function processAssert(
        CatalogCategoryView $catalogCategoryView,
        CmsIndex $cmsIndex,
        FixtureInterface $product,
        Category $category
    ) {
        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategoryByName($category->getCategoryName());

        $isProductVisible = $catalogCategoryView->getListProductBlock()->isProductVisible($product->getName());
        while (!$isProductVisible && $catalogCategoryView->getToolbar()->nextPage()) {
            $isProductVisible = $catalogCategoryView->getListProductBlock()->isProductVisible($product->getName());
        }

        \PHPUnit_Framework_Assert::assertTrue(
            $isProductVisible,
            'Product is absent on category page.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is visible in the assigned category.';
    }
}

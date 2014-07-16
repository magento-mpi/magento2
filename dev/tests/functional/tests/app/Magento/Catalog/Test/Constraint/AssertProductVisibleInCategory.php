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
use Magento\Catalog\Test\Fixture\CatalogCategory;
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
     * Displays an error message
     *
     * @var string
     */
    protected $errorMessage = 'Product is absent on category page.';

    /**
     * Message for passing test
     *
     * @var string
     */
    protected $successfulMessage = 'Product is visible in the assigned category.';

    /**
     * Assert that product is visible in the assigned category
     *
     * @param CatalogCategoryView $catalogCategoryView
     * @param CmsIndex $cmsIndex
     * @param FixtureInterface $product
     * @param CatalogCategory $category
     * @return void
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function processAssert(
        CatalogCategoryView $catalogCategoryView,
        CmsIndex $cmsIndex,
        FixtureInterface $product,
        CatalogCategory $category
    ) {
        $categoryName = $category->getName();
        if ($product->hasData('category_ids')) {
            $categoryIds = $product->getCategoryIds();
            $categoryName = is_array($categoryIds) ? reset($categoryIds) : $categoryName;
        }
        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategoryByName($categoryName);

        $isProductVisible = $catalogCategoryView->getListProductBlock()->isProductVisible($product->getName());
        while (!$isProductVisible && $catalogCategoryView->getToolbar()->nextPage()) {
            $isProductVisible = $catalogCategoryView->getListProductBlock()->isProductVisible($product->getName());
        }

        $quantityAndStockStatus = $product->getQuantityAndStockStatus();
        $stockStatus = isset($quantityAndStockStatus['is_in_stock'])
            ? $quantityAndStockStatus['is_in_stock']
            : null;
        if (($product->getVisibility() === 'Search') || ($stockStatus === 'Out of Stock')) {
            $isProductVisible = !$isProductVisible;
            $this->errorMessage = 'Product found in this category.';
            $this->successfulMessage = 'Asserts that the product could not be found in this category.';
        }

        \PHPUnit_Framework_Assert::assertTrue(
            $isProductVisible,
            $this->errorMessage
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return $this->successfulMessage;
    }
}

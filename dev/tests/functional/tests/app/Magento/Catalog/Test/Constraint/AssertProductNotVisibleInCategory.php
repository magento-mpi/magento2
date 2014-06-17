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
 * Class AssertProductNotVisibleInCategory
 */
class AssertProductNotVisibleInCategory extends AbstractConstraint
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
    protected $errorMessage = 'Product is exist on category page.';

    /**
     * Message for passing test
     *
     * @var string
     */
    protected $successfulMessage = 'Product is absent in the assigned category.';

    /**
     * Assert that product is not visible in the assigned category
     *
     * @param CatalogCategoryView $catalogCategoryView
     * @param CmsIndex $cmsIndex
     * @param FixtureInterface $product
     * @param CatalogCategory|null $category
     * @return void
     */
    public function processAssert(
        CatalogCategoryView $catalogCategoryView,
        CmsIndex $cmsIndex,
        FixtureInterface $product,
        CatalogCategory $category = null
    ) {
        $categoryName = $category
            ? $category->getName()
            : $product->getCategoryIds()[0];
        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategoryByName($categoryName);

        $isProductVisible = $catalogCategoryView->getListProductBlock()->isProductVisible($product->getName());
        while (!$isProductVisible && $catalogCategoryView->getToolbar()->nextPage()) {
            $isProductVisible = $catalogCategoryView->getListProductBlock()->isProductVisible($product->getName());
        }

        if ($product->getVisibility() === 'Search' || $product->getQuantityAndStockStatus() === 'Out of Stock') {
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

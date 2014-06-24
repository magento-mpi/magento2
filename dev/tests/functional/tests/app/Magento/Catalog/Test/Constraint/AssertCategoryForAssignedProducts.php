<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Class AssertCategoryForAssignedProducts
 * Assert that displayed assigned products on category page equals passed from fixture
 */
class AssertCategoryForAssignedProducts extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that displayed assigned products on category page equals passed from fixture
     *
     * @param CatalogCategory $category
     * @param CatalogCategoryView $categoryView
     * @return void
     */
    public function processAssert(CatalogCategory $category, CatalogCategoryView $categoryView)
    {
        $products = $category->getDataFieldConfig('products_name')['source']->getProduct();
        foreach ($products as $productFixture) {
            \PHPUnit_Framework_Assert::assertTrue(
                $categoryView->getListProductBlock()->isProductVisible($productFixture->getName()),
                "Products '{$productFixture->getName()}' not find."
            );
        }
    }

    /**
     * Displayed assigned products on category page equals passed from fixture
     *
     * @return string
     */
    public function toString()
    {
        return 'Displayed assigned products on category page equals passed from fixture.';
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Client\Browser;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Class AssertCategoryIsNotIncludeInMenu
 * Assert that the category is no longer available on the top menu bar
 */
class AssertCategoryIsNotIncludeInMenu extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that the category is no longer available on the top menu bar
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogCategory $category
     * @param Browser $browser
     * @param CatalogCategoryView $categoryView
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CatalogCategory $category,
        Browser $browser,
        CatalogCategoryView $categoryView
    ) {
        $cmsIndex->open();
        \PHPUnit_Framework_Assert::assertFalse(
            $cmsIndex->getTopmenu()->isVisibleCategory($category->getName()),
            'Category can be accessed from the navigation bar in the frontend.'
        );

        $browser->open($_ENV['app_frontend_url'] . $category->getUrlKey() . '.html');
        \PHPUnit_Framework_Assert::assertEquals(
            $category->getName(),
            $categoryView->getTitleBlock()->getTitle(),
            'Wrong page is displayed.'
        );
        $products = $category->getDataFieldConfig('products_name')['source']->getProducts();
        foreach ($products as $productFixture) {
            \PHPUnit_Framework_Assert::assertTrue(
                $categoryView->getListProductBlock()->isProductVisible($productFixture->getName()),
                "Products '{$productFixture->getName()}' not find."
            );
        }
    }

    /**
     * Category is no longer available on the top menu bar, but can be viewed by URL with all assigned products
     *
     * @return string
     */
    public function toString()
    {
        return 'Category is not on the top menu bar, but can be viewed by URL.';
    }
}

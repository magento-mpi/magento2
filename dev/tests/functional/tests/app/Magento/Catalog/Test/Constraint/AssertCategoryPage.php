<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint; 

use Magento\Catalog\Test\Fixture\CatalogCategoryEntity;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCategoryPage
 * Assert that displayed category data on category page equals to passed from fixture
 */
class AssertCategoryPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that displayed category data on category page equals to passed from fixture
     *
     * @param CatalogCategoryEntity $category
     * @param CatalogCategoryView $categoryView
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        CatalogCategoryEntity $category,
        CatalogCategoryView $categoryView,
        Browser $browser
    ) {
        $url = $_ENV['app_frontend_url'] . strtolower($category->getUrlKey()) . '.html';
        $browser->open($url);
        \PHPUnit_Framework_Assert::assertEquals(
            $url,
            $browser->getUrl(),
            'Wrong success message is displayed.'
            . "\nExpected: " . $url
            . "\nActual: " . $browser->getUrl()
        );
        $title = $categoryView->getTitleBlock()->getTitle();
        \PHPUnit_Framework_Assert::assertEquals(
            $category->getName(),
            $title,
            'Wrong page title.'
            . "\nExpected: " . $category->getName()
            . "\nActual: " . $title
        );
        $categoryDescription = $category->getDescription();
        if ($categoryDescription) {
            $description = $categoryView->getDescriptionBlock()->getDescription();
            \PHPUnit_Framework_Assert::assertEquals(
                $categoryDescription,
                $description,
                'Wrong description.'
                . "\nExpected: " . $categoryDescription
                . "\nActual: " . $description
            );
        }
        $sortBy = strtolower($category->getDefaultSortBy());
        if ($sortBy) {
            $sortType = $categoryView->getToolbar()->getSelectSortType();
            \PHPUnit_Framework_Assert::assertEquals(
                $sortBy,
                $sortType,
                'Wrong sorting type.'
                . "\nExpected: " . $sortBy
                . "\nActual: " . $sortType
            );
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Category data on category page equals to passed from fixture.';
    }
}

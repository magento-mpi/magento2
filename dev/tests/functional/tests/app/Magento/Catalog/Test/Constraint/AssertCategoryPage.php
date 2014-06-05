<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Mtf\Client\Browser;
use Mtf\Fixture\FixtureFactory;
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
     * @param CatalogCategory $category
     * @param CatalogCategory $initialCategory
     * @param FixtureFactory $fixtureFactory
     * @param CatalogCategoryView $categoryView
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        CatalogCategory $category,
        CatalogCategory $initialCategory,
        FixtureFactory $fixtureFactory,
        CatalogCategoryView $categoryView,
        Browser $browser
    ) {
        $product = $fixtureFactory->createByCode(
            'catalogProductSimple',
            [
                'dataSet' => 'product_without_category',
                'data' => [
                    'category_ids' => [
                        'category' => $initialCategory
                    ]
                ]
            ]
        );
        $product->persist();
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
            $description = $categoryView->getViewBlock()->getDescription();
            \PHPUnit_Framework_Assert::assertEquals(
                $categoryDescription,
                $description,
                'Wrong category description.'
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
        $availableSortType = array_filter(
            $category->getAvailableSortBy(),
            function (&$value) {
                return $value !== '-' && ucfirst($value);
            }
        );
        if ($availableSortType) {
            $availableSortType = array_values($availableSortType);
            $availableSortTypeOnPage = $categoryView->getToolbar()->getSortType();
            \PHPUnit_Framework_Assert::assertEquals(
                $availableSortType,
                $availableSortTypeOnPage,
                'Wrong available sorting type.'
                . "\nExpected: " . implode(PHP_EOL, $availableSortType)
                . "\nActual: " . implode(PHP_EOL, $availableSortTypeOnPage)
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

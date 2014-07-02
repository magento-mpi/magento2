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
use Magento\CatalogSearch\Test\Page\CatalogsearchResult;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Class AssertProductIsNotDisplayingOnFrontend
 */
class AssertProductIsNotDisplayingOnFrontend extends AbstractConstraint
{
    /**
     * Message on the product page 404
     */
    const NOT_FOUND_MESSAGE = 'Whoops, our bad...';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that product with current configurations is not displayed on front-end
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogsearchResult $catalogSearchResult
     * @param CatalogCategoryView $catalogCategoryView
     * @param CmsIndex $cmsIndex
     * @param FixtureInterface $product
     * @param CatalogCategory $category
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogsearchResult $catalogSearchResult,
        CatalogCategoryView $catalogCategoryView,
        CmsIndex $cmsIndex,
        FixtureInterface $product,
        CatalogCategory $category
    ) {
        $errors = [];
        // Check the product page is not available
        // TODO fix initialization url for frontend page
        $catalogProductView->init($product);
        $catalogProductView->open();
        $titleBlock = $catalogProductView->getTitleBlock();

        if ($titleBlock->getTitle() !== self::NOT_FOUND_MESSAGE) {
            $errors[] = '- the headline on the page does not match, the text should be -> "'
                . self::NOT_FOUND_MESSAGE . '".';
        }

        $cmsIndex->open();
        $cmsIndex->getSearchBlock()->search($product->getSku());
        if ($catalogSearchResult->getListProductBlock()->isProductVisible($product->getName())) {
            $errors[] = '- successful product search.';
        }

        $categoryName = ($product->hasData('category_ids'))
            ? $product->getCategoryIds()[0]['name']
            : $category->getName();
        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategoryByName($categoryName);
        $isProductVisible = $catalogCategoryView->getListProductBlock()->isProductVisible($product->getName());
        while (!$isProductVisible && $catalogCategoryView->getToolbar()->nextPage()) {
            $isProductVisible = $catalogCategoryView->getListProductBlock()->isProductVisible($product->getName());
        }

        if ($isProductVisible) {
            $errors[] = '- product found in this category.';
        }

        \PHPUnit_Framework_Assert::assertTrue(
            empty($errors),
            "In the process of checking product availability on the frontend, found the following errors:\n"
            . implode("\n", $errors)
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Assertion that the product is not available on the pages of the frontend.';
    }
}

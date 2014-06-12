<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\CatalogSearch\Test\Page\AdvancedResult;
use Magento\CatalogSearch\Test\Fixture\CatalogSearchQuery;

/**
 * Class AssertCatalogSearchResult
 */
class AssertCatalogSearchResult extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that result page contains all products, according to search request, from fixture
     *
     * @param array $products
     * @param AdvancedResult $resultPage
     * @param CatalogSearchQuery $catalogSearch
     * @return void
     */
    public function processAssert(array $products, AdvancedResult $resultPage, CatalogSearchQuery $catalogSearch)
    {

        $name = $products[$catalogSearch->getQueryText()]->getName();
        $isProductVisible = $resultPage->getListProductBlock()->isProductVisible($name);
        while (!$isProductVisible && $resultPage->getToolbar()->nextPage()) {
            $isProductVisible = $resultPage->getListProductBlock()->isProductVisible($name);
        }

        \PHPUnit_Framework_Assert::assertTrue(
            $isProductVisible,
            "'{$name}' product was not found on the page."
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Product successfully found.';
    }
}

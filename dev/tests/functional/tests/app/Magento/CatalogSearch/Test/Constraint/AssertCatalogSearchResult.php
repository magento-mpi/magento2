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
     * @return void
     */
    public function processAssert(array $products, AdvancedResult $resultPage)
    {
        $errors = [];
        foreach ($products as $product) {
            $name = $product->getName();
            $isProductVisible = $resultPage->getListProductBlock()->isProductVisible($name);
            while (!$isProductVisible && $resultPage->getToolbar()->nextPage()) {
                $isProductVisible = $resultPage->getListProductBlock()->isProductVisible($name);
            }

            if ($isProductVisible === false) {
                $errors[] = '- ' . $name;
            }
        }

        \PHPUnit_Framework_Assert::assertTrue(
            empty($errors),
            'Were not found the following products:' . implode("\n", $errors)
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'All products have been successfully found.';
    }
}

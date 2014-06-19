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
use Magento\CatalogSearch\Test\Page\CatalogsearchResult;

/**
 * Class AssertProductSearchableBySku
 */
class AssertProductSearchableBySku extends AbstractConstraint
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
    protected $errorMessage = 'The product has not been found by SKU';

    /**
     * Message for passing test
     *
     * @var string
     */
    protected $successfulMessage = 'Product successfully found by SKU.';

    /**
     * Assert that product can be searched via Quick Search using searchable product attributes (Search by SKU)
     *
     * @param CatalogsearchResult $catalogSearchResult
     * @param CmsIndex $cmsIndex
     * @param FixtureInterface $product
     * @return void
     */
    public function processAssert(
        CatalogsearchResult $catalogSearchResult,
        CmsIndex $cmsIndex,
        FixtureInterface $product
    ) {
        $cmsIndex->open();
        $cmsIndex->getSearchBlock()->search($product->getSku());

        if ($product->getVisibility() === 'Catalog' || $product->getQuantityAndStockStatus() === 'Out of Stock') {
            $isVisible = !($catalogSearchResult->getListProductBlock()->isProductVisible($product->getName()));
            $this->errorMessage = 'Product successfully found by SKU.';
            $this->successfulMessage = 'The product has not been found by SKU.';
        } else {
            $isVisible = $catalogSearchResult->getListProductBlock()->isProductVisible($product->getName());
        }

        \PHPUnit_Framework_Assert::assertTrue(
            $isVisible,
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

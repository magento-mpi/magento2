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
        $this->searchBy($cmsIndex, $product);

        $quantityAndStockStatus = $product->getQuantityAndStockStatus();
        $stockStatus = isset($quantityAndStockStatus['is_in_stock'])
            ? $quantityAndStockStatus['is_in_stock']
            : null;

        $isVisible = $catalogSearchResult->getListProductBlock()->isProductVisible($product->getName());
        while (!$isVisible && $catalogSearchResult->getToolbar()->nextPage()) {
            $isVisible = $catalogSearchResult->getListProductBlock()->isProductVisible($product->getName());
        }

        if ($product->getVisibility() === 'Catalog' || $stockStatus === 'Out of Stock') {
            $isVisible = !$isVisible;
            $this->errorMessage = 'Product successfully found by SKU.';
            $this->successfulMessage = 'The product has not been found by SKU.';
        }

        \PHPUnit_Framework_Assert::assertTrue(
            $isVisible,
            $this->errorMessage
        );
    }

    /**
     * Search product on frontend
     *
     * @param CmsIndex $cmsIndex
     * @param FixtureInterface $product
     */
    protected function searchBy(CmsIndex $cmsIndex, FixtureInterface $product)
    {
        $cmsIndex->getSearchBlock()->search($product->getSku());
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

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\CatalogSearch\Test\Page\CatalogsearchResult;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertProductSearchableBySku
 *
 * @package Magento\Catalog\Test\Constraint
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
     * Assert that product can be searched via Quick Search using searchable product attributes (Search by SKU).
     *
     * @param CatalogsearchResult $catalogSearchResult
     * @param CmsIndex $cmsIndex
     * @param CatalogProductSimple $product
     * @return void
     */
    public function processAssert(
        CatalogsearchResult $catalogSearchResult,
        CmsIndex $cmsIndex,
        CatalogProductSimple $product
    ) {
        $cmsIndex->open();
        $cmsIndex->getSearchBlock()->search($product->getSku());
        \PHPUnit_Framework_Assert::assertTrue(
            $catalogSearchResult->getListProductBlock()->isProductVisible($product->getName()),
            'Product was not found by SKU.'
        );
    }

    /**
     * Text of Searchable assert
     *
     * @return string
     */
    public function toString()
    {
        return "Product is searchable by SKU.";
    }
}

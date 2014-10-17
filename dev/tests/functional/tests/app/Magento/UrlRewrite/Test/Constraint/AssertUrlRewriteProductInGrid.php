<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlRewriteIndex;

/**
 * Class AssertUrlRewriteProductInGrid
 * Assert that url product in grid.
 */
class AssertUrlRewriteProductInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that url rewrite product in grid.
     *
     * @param CatalogProductSimple $product
     * @param CatalogProductSimple $initialProduct
     * @param UrlRewriteIndex $urlRewriteIndex
     * @return void
     */
    public function processAssert(
        CatalogProductSimple $product,
        CatalogProductSimple $initialProduct,
        UrlRewriteIndex $urlRewriteIndex
    ) {
        $urlRewriteIndex->open();
        $url = strtolower($initialProduct->getCategoryIds()[0] . '/' . $product->getUrlKey());
        \PHPUnit_Framework_Assert::assertTrue(
            $urlRewriteIndex->getUrlRedirectGrid()->isRowVisible(['target_path' => $url], true, false),
            'URL Rewrite with request path "' . $url . '" is absent in grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'URL Rewrite is present in grid.';
    }
}

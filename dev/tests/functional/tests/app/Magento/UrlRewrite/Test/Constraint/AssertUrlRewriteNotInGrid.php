<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Constraint;

use Magento\UrlRewrite\Test\Fixture\UrlRewrite;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertUrlRewriteNotInGrid
 * Assert that url rewrite category not in grid
 */
class AssertUrlRewriteNotInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that url rewrite not in grid
     *
     * @param UrlrewriteIndex $urlRewriteIndex
     * @param UrlRewrite $productRedirect
     * @return void
     */
    public function processAssert(UrlrewriteIndex $urlRewriteIndex, UrlRewrite $productRedirect)
    {
        $urlRewriteIndex->open();
        $filter = ['request_path' => $productRedirect->getRequestPath()];
        \PHPUnit_Framework_Assert::assertFalse(
            $urlRewriteIndex->getUrlRedirectGrid()->isRowVisible($filter),
            'URL Redirect with request path \'' . $productRedirect->getRequestPath() . '\' is present in grid.'
        );
    }

    /**
     * URL rewrite category not present in grid
     *
     * @return string
     */
    public function toString()
    {
        return 'URL Redirect is not present in grid.';
    }
}

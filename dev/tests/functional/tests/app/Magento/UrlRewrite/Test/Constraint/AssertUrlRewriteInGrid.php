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
 * Class AssertUrlRewriteInGrid
 * Assert that url rewrite category in grid
 */
class AssertUrlRewriteInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that url rewrite category in grid
     *
     * @param UrlrewriteIndex $urlRewriteIndex
     * @param UrlRewrite $urlRewrite
     * @return void
     */
    public function processAssert(UrlrewriteIndex $urlRewriteIndex, UrlRewrite $urlRewrite)
    {
        $urlRewriteIndex->open();
        $filter = ['request_path' => $urlRewrite->getRequestPath()];
        \PHPUnit_Framework_Assert::assertTrue(
            $urlRewriteIndex->getUrlRedirectGrid()->isRowVisible($filter),
            'URL Redirect with request path \'' . $urlRewrite->getRequestPath() . '\' is absent in grid.'
        );
    }

    /**
     * URL rewrite category present in grid
     *
     * @return string
     */
    public function toString()
    {
        return 'URL Redirect is present in grid.';
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Constraint;

use Magento\Backend\Test\Fixture\UrlRewriteCategory;
use Magento\Backend\Test\Page\Adminhtml\UrlrewriteIndex;
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
     * @param UrlRewriteCategory $urlRewriteCategory
     * @return void
     */
    public function processAssert(UrlrewriteIndex $urlRewriteIndex, UrlRewriteCategory $urlRewriteCategory)
    {
        $urlRewriteIndex->open();
        $filter = ['request_path' => $urlRewriteCategory->getRequestPath()];
        \PHPUnit_Framework_Assert::assertTrue(
            $urlRewriteIndex->getUrlRedirectGrid()->isRowVisible($filter),
            'URL Redirect with request path \'' . $urlRewriteCategory->getRequestPath() . '\' is absent in grid.'
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

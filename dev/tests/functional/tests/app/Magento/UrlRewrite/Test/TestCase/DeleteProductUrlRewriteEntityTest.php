<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\UrlRewrite\Test\TestCase;

use Magento\UrlRewrite\Test\Fixture\UrlRewrite;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlRewriteEdit;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlRewriteIndex;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for DeleteProductUrlRewritesEntity
 *
 * Precondition:
 * 1. Sub category is created.
 * 2. Product is created.
 * 3. Product url rewrites is created.
 *
 * Test Flow:
 * 1. Login to backend.
 * 2. Navigate to MARKETING > URL Rewrites.
 * 3. Click Redirect from grid.
 * 4. Click 'Delete' button.
 * 5. Perform asserts.
 *
 * @group URL_Rewrites_(MX)
 * @ZephyrId  MAGETWO-23287
 */
class DeleteProductUrlRewriteEntityTest extends Injectable
{
    /**
     * Url rewrite index page
     *
     * @var UrlRewriteIndex
     */
    protected $urlRewriteIndex;

    /**
     * Url rewrite edit page
     *
     * @var UrlRewriteEdit
     */
    protected $urlRewriteEdit;

    /**
     * Prepare pages
     *
     * @param UrlRewriteIndex $urlRewriteIndex
     * @param UrlRewriteEdit $urlRewriteEdit
     * @return void
     */
    public function __inject(
        UrlRewriteIndex $urlRewriteIndex,
        UrlRewriteEdit $urlRewriteEdit
    ) {
        $this->urlRewriteIndex = $urlRewriteIndex;
        $this->urlRewriteEdit = $urlRewriteEdit;
    }

    /**
     * Delete product url rewrites entity
     *
     * @param UrlRewrite $productRedirect
     * @return void
     */
    public function test(UrlRewrite $productRedirect)
    {
        // Precondition
        $productRedirect->persist();
        // Steps
        $this->urlRewriteIndex->open();
        $filter = ['request_path' => $productRedirect->getRequestPath()];
        $this->urlRewriteIndex->getUrlRedirectGrid()->searchAndOpen($filter);
        $this->urlRewriteEdit->getPageMainActions()->delete();
    }
}

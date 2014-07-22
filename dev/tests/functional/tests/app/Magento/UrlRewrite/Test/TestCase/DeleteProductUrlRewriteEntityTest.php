<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\TestCase;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\UrlRewrite\Test\Fixture\UrlRewrite;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteEdit;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteIndex;
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
     * @var UrlrewriteIndex
     */
    protected $urlRewriteIndex;

    /**
     * Url rewrite edit page
     *
     * @var UrlrewriteEdit
     */
    protected $urlRewriteEdit;

    /**
     * Prepare pages
     *
     * @param UrlrewriteIndex $urlRewriteIndex
     * @param UrlrewriteEdit $urlRewriteEdit
     * @return void
     */
    public function __inject(
        UrlrewriteIndex $urlRewriteIndex,
        UrlrewriteEdit $urlRewriteEdit
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
    public function testDeleteProductUrlRewrite(UrlRewrite $productRedirect)
    {
        // Precondition
        $productRedirect->persist();
        // Steps
        $this->urlRewriteIndex->open();
        $filter = ['request_path' => $productRedirect->getRequestPath()];
        $this->urlRewriteIndex->getUrlRewriteGrid()->searchAndOpen($filter);
        $this->urlRewriteEdit->getPageMainActions()->delete();
    }
}

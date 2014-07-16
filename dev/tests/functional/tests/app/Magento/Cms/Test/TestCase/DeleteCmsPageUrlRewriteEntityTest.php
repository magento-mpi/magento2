<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Cms\Test\Fixture\UrlRewrite;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteEdit;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteIndex;

/**
 * Test Creation for DeleteCmsPageUrlRewriteEntityTest
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create CMS Page
 * 2. Create CMS Page URL Redirect
 *
 * Steps:
 * 1. Login to backend as Admin
 * 2. Go to the Marketing-> SEO & Search->URL Redirects
 * 3. Search and open created URL Redirect
 * 4. Delete Redirect
 * 5. Perform all assertions
 *
 * @group URL_Rewrites_(PS)
 * @ZephyrId MAGETWO-25915
 */
class DeleteCmsPageUrlRewriteEntityTest extends Injectable
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
     * Inject pages
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
     * Delete CMS page rewrites entity
     *
     * @param UrlRewrite $urlRewrite
     * @return void
     */
    public function test(UrlRewrite $urlRewrite)
    {
        //Precondition
        $urlRewrite->persist();

        //Steps
        $this->urlRewriteIndex->open();
        $filter = ['request_path' => $urlRewrite->getRequestPath()];
        $this->urlRewriteIndex->getUrlRedirectGrid()->searchAndOpen($filter);
        $this->urlRewriteEdit->getPageMainActions()->delete();
    }
}

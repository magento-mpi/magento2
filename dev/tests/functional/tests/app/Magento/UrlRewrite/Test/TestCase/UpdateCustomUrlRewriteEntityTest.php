<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\UrlRewrite\Test\Fixture\UrlRewrite;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteEdit;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteIndex;

/**
 * Test Creation for UpdateCustomUrlRewritesEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create default simple product
 * 2. Create custom url rewrite
 *
 * Steps:
 * 1. Login to backend as Admin
 * 2. Go to the Marketing-> SEO & Search->URL Redirects
 * 3. Search and open created URL Redirect
 * 4. Fill data according to data set
 * 5. Save Redirect
 * 6. Perform all assertions
 *
 * @group URL_Rewrites_(PS)
 * @ZephyrId MAGETWO-25784
 */
class UpdateCustomUrlRewriteEntityTest extends Injectable
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
    public function __inject(UrlrewriteIndex $urlRewriteIndex, UrlrewriteEdit $urlRewriteEdit)
    {
        $this->urlRewriteIndex = $urlRewriteIndex;
        $this->urlRewriteEdit = $urlRewriteEdit;
    }

    /**
     * Update custom URL Rewrite
     *
     * @param UrlRewrite $rewrite
     * @param UrlRewrite $urlRewrite
     * @return void
     */
    public function test(UrlRewrite $rewrite, UrlRewrite $urlRewrite)
    {
        $rewrite->persist();
        //Steps
        $this->urlRewriteIndex->open();
        $filter = ['request_path' => $rewrite->getRequestPath()];
        $this->urlRewriteIndex->getUrlRedirectGrid()->searchAndOpen($filter);
        $this->urlRewriteEdit->getFormBlock()->fill($urlRewrite, null, 'target_path', $rewrite);
        $this->urlRewriteEdit->getPageMainActions()->save();
    }
}

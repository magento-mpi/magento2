<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\TestCase;

use Magento\UrlRewrite\Test\Fixture\UrlRewrite;
use Mtf\TestCase\Injectable;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteEdit;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteIndex;

/**
 * Test Creation for Delete Category URL Rewrites Entity
 *
 * Test Flow:
 * Preconditions:
 * 1. Create category
 * 2. Create custom category UrlRewrite
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Marketing->URL Rewrites
 * 3. Search and open created URL Rewrite
 * 4. Delete URL Rewrite
 * 5. Perform all assertions
 *
 * @group URL_Rewrites_(MX)
 * @ZephyrId MAGETWO-25086
 */
class DeleteCategoryUrlRewriteEntityTest extends Injectable
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
     * Delete category Url Rewrite
     *
     * @param UrlRewrite $urlRewrite
     * @return void
     */
    public function testDeleteCategoryUrlRewrite(UrlRewrite $urlRewrite)
    {
        //Precondition
        $urlRewrite->persist();
        //Steps
        $this->urlRewriteIndex->open();
        if ($urlRewrite->getRequestPath()) {
            $filter = ['request_path' => $urlRewrite->getRequestPath()];
        } else {
            $filter = ['id_path' => $urlRewrite->getIdPath()];
        }
        $this->urlRewriteIndex->getUrlRewriteGrid()->searchAndOpen($filter);
        $this->urlRewriteEdit->getPageMainActions()->delete();
    }
}

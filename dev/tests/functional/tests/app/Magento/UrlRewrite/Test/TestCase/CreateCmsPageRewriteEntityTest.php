<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\TestCase;

use Magento\UrlRewrite\Test\Page\Adminhtml\EditCmsPage;
use Mtf\TestCase\Injectable;
use Magento\Cms\Test\Fixture\CmsPage;
use Magento\UrlRewrite\Test\Fixture\UrlRewrite;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteEdit;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteIndex;

/**
 * Test Creation for CreateCmsPageRewriteEntity
 *
 * Test Flow:
 *
 * Preconditions
 * 1. Create CMS-Page CmsPage.php
 *
 * Steps
 * 1. Login to backend as Admin
 * 2. Go to the Marketing-> SEO & Search->URL Redirects
 * 3. Click "Add Url Rewrite" button
 * 4. Select "For CMS Page" in Create URL Rewrite dropdown
 * 5. Select CMS page from preconditions in grid
 * 6. Fill data according to data set
 * 7. Save Rewrite
 * 8. Perform all assertions
 *
 * @group URL_Rewrites_(PS)
 * @ZephyrId MAGETWO-24847
 */
class CreateCmsPageRewriteEntityTest extends Injectable
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
     * Edit Cms Page url rewrite
     *
     * @var EditCmsPage
     */
    protected $editCmsPage;

    /**
     * Inject pages
     *
     * @param UrlrewriteIndex $urlRewriteIndex
     * @param UrlrewriteEdit $urlRewriteEdit
     * @param EditCmsPage $editCmsPage
     * @return void
     */
    public function __inject(
        UrlrewriteIndex $urlRewriteIndex,
        UrlrewriteEdit $urlRewriteEdit,
        EditCmsPage $editCmsPage
    ) {
        $this->urlRewriteIndex = $urlRewriteIndex;
        $this->urlRewriteEdit = $urlRewriteEdit;
        $this->editCmsPage = $editCmsPage;
    }

    /**
     * Create CMS page rewrites
     *
     * @param CmsPage $cmsPage
     * @param UrlRewrite $urlRewrite
     * @return void
     */
    public function testCmsPageRewrite(CmsPage $cmsPage, UrlRewrite $urlRewrite)
    {
        $cmsPage->persist();
        $this->urlRewriteIndex->open();
        $this->urlRewriteIndex->getPageActionsBlock()->addNew();
        $this->urlRewriteEdit->getUrlRewriteTypeSelectorBlock()->selectType('For CMS page');
        $filter = ['title' => $cmsPage->getTitle()];
        $this->editCmsPage->getGridBlock()->searchAndOpen($filter);
        $this->urlRewriteEdit->getFormBlock()->fill($urlRewrite);
        $this->urlRewriteEdit->getPageMainActions()->save();
    }
}

<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Cms\Test\TestCase;

use Magento\Cms\Test\Fixture\UrlRewrite;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlRewriteEdit;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlRewriteIndex;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for CreateCmsPageRewriteEntity
 *
 * Test Flow:
 *
 * Preconditions
 * 1. Create CMS-Page
 *
 * Steps
 * 1. Login to backend as Admin
 * 2. Go to the Marketing-> SEO & Search->URL Rewrites
 * 3. Click "Add Url Rewrite" button
 * 4. Select "For CMS Page" in Create URL Rewrite dropdown
 * 5. Select CMS page from preconditions in grid
 * 6. Fill data according to data set
 * 7. Save Rewrite
 * 8. Perform all assertions
 *
 * @group URL_Rewrites_(MX)
 * @ZephyrId MAGETWO-24847
 */
class CreateCmsPageRewriteEntityTest extends Injectable
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
     * Inject pages
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
     * Create CMS page rewrites
     *
     * @param UrlRewrite $urlRewrite
     * @return array
     */
    public function test(UrlRewrite $urlRewrite)
    {
        //Steps
        $this->urlRewriteIndex->open();
        $this->urlRewriteIndex->getPageActionsBlock()->addNew();
        $this->urlRewriteEdit->getFormBlock()->fill($urlRewrite);
        $cmsPage = $urlRewrite->getDataFieldConfig('target_path')['source']->getEntity();
        $filter = ['title' => $cmsPage->getTitle()];
        $this->urlRewriteEdit->getCmsGridBlock()->searchAndOpen($filter);
        $this->urlRewriteEdit->getFormBlock()->fill($urlRewrite);
        $this->urlRewriteEdit->getPageMainActions()->save();

        return ['cmsPage' => $cmsPage];
    }
}

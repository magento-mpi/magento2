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
use Magento\Backend\Test\Page\Adminhtml\StoreNew;
use Magento\Backend\Test\Page\Adminhtml\StoreIndex;
use Magento\Backend\Test\Page\Adminhtml\StoreDelete;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteEdit;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteIndex;

/**
 * Test Creation for UpdateCmsPageRewriteEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Create custom store view
 * 2. Create CMS Page
 * 3. Create CMS Page URL Redirect
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
 * @ZephyrId MAGETWO-26173
 */
class UpdateCmsPageRewriteEntityTest extends Injectable
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
     * Page StoreIndex
     *
     * @var StoreIndex
     */
    protected $storeIndex;

    /**
     * Page StoreNew
     *
     * @var StoreNew
     */
    protected $storeNew;

    /**
     * Page StoreDelete
     *
     * @var StoreDelete
     */
    protected $storeDelete;

    /**
     * Store Name
     *
     * @var string
     */
    protected $storeName;

    /**
     * Skipped stores for tearDown
     *
     * @var array
     */
    protected $skippedStores = [
        'Main Website/Main Website Store/Default Store View',
    ];

    /**
     * Inject pages
     *
     * @param UrlrewriteIndex $urlRewriteIndex
     * @param UrlrewriteEdit $urlRewriteEdit
     * @param StoreIndex $storeIndex
     * @param StoreNew $storeNew
     * @param StoreDelete $storeDelete
     * @return void
     */
    public function __inject(
        UrlrewriteIndex $urlRewriteIndex,
        UrlrewriteEdit $urlRewriteEdit,
        StoreIndex $storeIndex,
        StoreNew $storeNew,
        StoreDelete $storeDelete
    ) {
        $this->urlRewriteIndex = $urlRewriteIndex;
        $this->urlRewriteEdit = $urlRewriteEdit;
        $this->storeIndex = $storeIndex;
        $this->storeNew = $storeNew;
        $this->storeDelete = $storeDelete;
    }

    /**
     * Update CMS page rewrites
     *
     * @param UrlRewrite $urlRewrite
     * @param UrlRewrite $cmsPageRewrite
     * @return array
     */
    public function test(UrlRewrite $urlRewrite, UrlRewrite $cmsPageRewrite)
    {
        //Preconditions
        $cmsPageRewrite->persist();

        //Steps
        $this->urlRewriteIndex->open();
        $this->storeName = $urlRewrite->getStoreId();
        $filter = ['request_path' => $cmsPageRewrite->getRequestPath()];
        $this->urlRewriteIndex->getUrlRedirectGrid()->searchAndOpen($filter);
        $this->urlRewriteEdit->getFormBlock()->fill($urlRewrite);
        $this->urlRewriteEdit->getPageMainActions()->save();

        return ['cmsPage' => $cmsPageRewrite->getDataFieldConfig('id_path')['source']->getEntity()];
    }

    /**
     * Delete Store after test
     *
     * @return void
     */
    public function tearDown()
    {
        if (!in_array($this->storeName, $this->skippedStores)) {
            $tmp = explode("/", $this->storeName);
            $filter['store_title'] = end($tmp);
            $this->storeIndex->open();
            $this->storeIndex->getStoreGrid()->searchAndOpen($filter);
            $this->storeNew->getFormPageActions()->delete();
            $this->storeDelete->getStoreForm()->fillForm(['create_backup' => 'No']);
            $this->storeDelete->getFormPageActions()->delete();
        }
    }
}

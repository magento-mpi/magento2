<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\TestCase;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Mtf\TestCase\Injectable;
use Magento\UrlRewrite\Test\Fixture\UrlRewrite;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteIndex;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteEdit;

/**
 * Test Creation for Product URL Rewrites Entity
 *
 * Test Flow:
 * Preconditions:
 * 1. Create custom storeView
 * 2. Create simple product
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Marketing->Url Redirects
 * 3. Click "Add URL Rewrite" button
 * 4. Select "For Product" from  "Create URL Rewrite:" dropdown
 * 5. Select created early product
 * 6. Click "Skip Category Selection" button
 * 7. Fill data according to dataSet
 * 8. Perform all assertions
 *
 * @group URL_Rewrites_(PS)
 * @ZephyrId MAGETWO-25150
 */
class ProductUrlRewriteEntityTest extends Injectable
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
     * Prepare dataSets and pages
     *
     * @param UrlrewriteIndex $urlRewriteIndex
     * @param UrlrewriteEdit $urlRewriteEdit
     * @return array
     */
    public function __inject(UrlrewriteIndex $urlRewriteIndex, UrlrewriteEdit $urlRewriteEdit)
    {
        $this->urlRewriteIndex = $urlRewriteIndex;
        $this->urlRewriteEdit = $urlRewriteEdit;
    }

    /**
     * Create product URL Rewrite
     *
     * @param CatalogProductSimple $product
     * @param UrlRewrite $urlRewrite
     */
    public function testProductUrlRewrite(CatalogProductSimple $product, UrlRewrite $urlRewrite)
    {
        //Precondition
        $product->persist();
        $filter = ['id' => $product->getId()];
        //Steps
        $this->urlRewriteIndex->open();
        $this->urlRewriteIndex->getPageActionsBlock()->addNew();
        $this->urlRewriteEdit->getUrlRewriteTypeSelectorBlock()->selectType('For product');
        $this->urlRewriteEdit->getProductGridBlock()->searchAndOpen($filter);
        $this->urlRewriteEdit->getTreeBlock()->skipCategorySelection();
        $this->urlRewriteEdit->getFormBlock()->fill($urlRewrite);
        $this->urlRewriteEdit->getPageMainActions()->save();
    }
}

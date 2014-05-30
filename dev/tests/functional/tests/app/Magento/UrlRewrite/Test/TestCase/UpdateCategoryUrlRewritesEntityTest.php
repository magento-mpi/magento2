<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\TestCase;

use Magento\Catalog\Test\Fixture\CatalogCategoryEntity;
use Mtf\TestCase\Injectable;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteIndex;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteEdit;
use Mtf\Fixture\FixtureFactory;
use Magento\UrlRewrite\Test\Fixture\UrlRewrite;

/**
 * Test Creation for UpdateCategoryUrlRewritesEntity
 *
 * Test Flow:
 *
 * Precondition:
 * 1. SubCategory is created.
 * 2. Category URL Rewrite is created.
 *
 * Steps:
 * 1. Log in to backend as Admin.
 * 2. Go to the Marketing-> SEO & Search->URL Redirects.
 * 3. Click Category URL Rewrite from grid.
 * 4. Edit test value(s) according to dataSet.
 * 5. Click 'Save' button.
 * 6. Perform all asserts.
 *
 * @group URL_Rewrites_(PS)
 * @ZephyrId MAGETWO-24838
 */
class UpdateCategoryUrlRewritesEntityTest extends Injectable
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
     * @param FixtureFactory $fixtureFactory
     * @param CatalogCategoryEntity $category
     * @return array
     */
    public function __inject(
        UrlrewriteIndex $urlRewriteIndex,
        UrlrewriteEdit $urlRewriteEdit,
        FixtureFactory $fixtureFactory,
        CatalogCategoryEntity $category
    ) {
        $this->urlRewriteIndex = $urlRewriteIndex;
        $this->urlRewriteEdit = $urlRewriteEdit;
        $category->persist();
        $categoriesRedirect = $fixtureFactory->createByCode(
            'urlRewrite',
            [
                'dataSet' => 'default',
                'data' => ['rewrite_path' => 'category/' . $category->getId()]
            ]
        );
        $categoriesRedirect->persist();

        return ['categoriesRedirect' => $categoriesRedirect, 'category' => $category];
    }

    /**
     * Update category URL rewrites
     *
     * @param UrlRewrite $categoriesRedirect
     * @param UrlRewrite $urlRewrite
     * @return void
     */
    public function testUpdateCategoryURLRewrites(UrlRewrite $categoriesRedirect, UrlRewrite $urlRewrite)
    {
        //Steps
        $this->urlRewriteIndex->open();
        $filter = ['request_path' => $categoriesRedirect->getRequestPath()];
        $this->urlRewriteIndex->getUrlRedirectGrid()->searchAndOpen($filter);
        $this->urlRewriteEdit->getFormBlock()->fill($urlRewrite);
        $this->urlRewriteEdit->getPageMainActions()->save();
    }
}

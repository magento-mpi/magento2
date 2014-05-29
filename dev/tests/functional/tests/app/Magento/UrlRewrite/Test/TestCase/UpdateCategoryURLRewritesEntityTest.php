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
 * Test Creation for UpdateCategoryURLRewritesEntity
 *
 * Test Flow:
 *
 * Precondition:
 * 1. SubCategory is created. Category
 * 2. Category URL Rewrite is created. CategoryRewrite
 *
 * Steps:
 * 1. Log in to backend as Admin
 * 2. Go to the Marketing-> SEO & Search->URL Redirects
 * 3. Click Category URL Rewrite from grid
 * 4. Edit test value(s) according to dataSet.
 * 5. Click 'Save' button.
 * 6. Perform all asserts
 *
 * @group URL_Rewrites_(PS)
 * @ZephyrId MAGETWO-24838
 */
class UpdateCategoryURLRewritesEntityTest extends Injectable
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
                'data' => ['rewrite_path' => 'product/' . $category->getId()]
            ]
        );
        $categoriesRedirect->persist();

        return ['categoriesRedirect' => $categoriesRedirect, 'category' => $category];
    }

    /**
     * Update category URL rewrites
     *
     * @param CatalogCategoryEntity $category
     * @param UrlRewrite $urlRewrite
     * @return void
     */
    public function testUpdateCategoryURLRewrites(CatalogCategoryEntity $category, UrlRewrite $urlRewrite)
    {
        //Steps
        $this->urlRewriteIndex->open();
        $this->urlRewriteIndex->getPageActionsBlock()->addNew();
        $this->urlRewriteEdit->getTreeBlock()->selectCategory($category->getName());
        $this->urlRewriteEdit->getFormBlock()->fill($urlRewrite);
        $this->urlRewriteEdit->getPageMainActions()->save();
    }
}

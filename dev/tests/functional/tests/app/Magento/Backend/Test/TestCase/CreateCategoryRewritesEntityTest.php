<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\TestCase;

use Magento\Backend\Test\Fixture\UrlRewriteCategory;
use Magento\Backend\Test\Page\Adminhtml\UrlrewriteEdit;
use Magento\Backend\Test\Page\Adminhtml\UrlrewriteIndex;
use Mtf\Fixture\FixtureFactory;
use Magento\Catalog\Test\Fixture\CatalogCategoryEntity;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for Create Category Rewrites Entity
 *
 * *Precondition:*
 * Create Sub-category
 *
 * Test Flow:
 * 1. Login to backend as Admin
 * 2. Go to the Marketing-> SEO & Search->URL Redirects
 * 3. Click "+" button
 * 4. Select "For Category" in Create URL Rewrite dropdown
 * 5. Select Category in "Category tree"
 * 6. Fill data according to data set
 * 7. Save Rewrite
 * 8. Verify created rewrite
 *
 * @group URL_Rewrites_(MX)
 * @ZephyrId MAGETWO-24280
 */
class CreateCategoryRewritesEntityTest extends Injectable
{
    /**
     * Page of url rewrite edit category
     *
     * @var UrlrewriteEdit
     */
    protected $urlRewriteEdit;

    /**
     * Main page of url rewrite
     *
     * @var UrlrewriteIndex
     */
    protected $urlRewriteIndex;

    /**
     * Inject page
     *
     * @param UrlrewriteEdit $urlRewriteEdit
     * @param UrlrewriteIndex $urlRewriteIndex
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __inject(
        UrlrewriteEdit $urlRewriteEdit,
        UrlRewriteIndex $urlRewriteIndex,
        FixtureFactory $fixtureFactory
    ) {
        $this->urlRewriteEdit = $urlRewriteEdit;
        $this->urlRewriteIndex = $urlRewriteIndex;
        $category = $fixtureFactory->createByCode(
            'catalogCategoryEntity',
            ['dataSet' => 'default_subcategory']
        );
        $category->persist();
        return ['category' => $category];
    }

    /**
     * Test check create category rewrites
     *
     * @param UrlRewriteCategory $urlRewriteCategory
     * @param CatalogCategoryEntity $category
     * @return void
     */
    public function testCreateCategoryRewrites(UrlRewriteCategory $urlRewriteCategory, CatalogCategoryEntity $category)
    {
        //Steps
        $this->urlRewriteIndex->open();
        $this->urlRewriteIndex->getPageActionsBlock()->addNew();
        $categoryName = $category->getName();
        $this->urlRewriteEdit->getTreeBlock()->selectCategory($categoryName);
        $this->urlRewriteEdit->getFormBlock()->fill($urlRewriteCategory);
        $this->urlRewriteEdit->getPageMainActions()->save();
    }
}

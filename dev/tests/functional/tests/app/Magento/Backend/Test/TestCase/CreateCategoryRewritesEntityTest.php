<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\TestCase;

use Magento\Backend\Test\Fixture\UrlRewriteCategory;
use Magento\Backend\Test\Page\Adminhtml\UrlRewriteEditCategory;
use Magento\Backend\Test\Page\Adminhtml\UrlRewriteIndex;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for Create Category Rewrites Entity
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
     * @var UrlRewriteEditCategory
     */
    private $urlRewriteEditCategory;

    /**
     * Main page of url rewrite
     *
     * @var UrlRewriteIndex
     */
    private $urlRewriteIndex;

    /**
     * Inject page
     *
     * @param UrlRewriteEditCategory $urlRewriteEditCategory
     * @param UrlRewriteIndex $urlRewriteIndex
     */
    public function __inject(
        UrlRewriteEditCategory $urlRewriteEditCategory,
        UrlRewriteIndex $urlRewriteIndex
    ) {
        $this->urlRewriteEditCategory = $urlRewriteEditCategory;
        $this->urlRewriteIndex = $urlRewriteIndex;
    }

    /**
     * Create category rewrites
     *
     * @param UrlRewriteCategory $urlRewriteCategory
     * @param FixtureFactory $fixtureFactory
     */
    public function testCreateCategoryRewrites(UrlRewriteCategory $urlRewriteCategory, FixtureFactory $fixtureFactory)
    {
        /** @var \Magento\Catalog\Test\Fixture\CatalogCategoryEntity $catalogCategoryEntity */
        $catalogCategoryEntity = $fixtureFactory->createByCode(
            'catalogCategoryEntity',
            ['dataSet' => 'default_subcategory']
        );
        $catalogCategoryEntity->persist();
        $this->urlRewriteIndex->open();
        $this->urlRewriteIndex->getGridPageActions()->addNew();
        $categoryName = $catalogCategoryEntity->getName();
        $this->urlRewriteEditCategory->getTreeBlock()->selectCategory($categoryName);
        $this->urlRewriteEditCategory->getFormBlock()->fill($urlRewriteCategory);
        $this->urlRewriteEditCategory->getPageMainActions()->save();
    }
}

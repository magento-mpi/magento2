<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\CatalogEvent\Test\Page\Product\CatalogProductView;
use Magento\CatalogEvent\Test\Fixture\CatalogEventEntity;
use Magento\CatalogEvent\Test\Page\Category\CatalogCategoryView;

/**
 * Class AssertCatalogEventBlockVisible
 * Check visible/invisible Event block on catalog page/product pages
 */
class AssertCatalogEventBlockVisible extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Category Page on Frontend
     *
     * @var CatalogCategoryView
     */
    protected $catalogCategoryView;

    /**
     * Index Page on Frontend
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Product Page on Frontend
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Category Name
     *
     * @var string
     */
    protected $categoryName;

    /**
     * Product Name
     *
     * @var string
     */
    protected $productName;

    /**
     * Assert that Event block is visible/invisible on page according to fixture(catalog page/product pages)
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogEventEntity $catalogEvent
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogProductSimple $catalogProductSimple
     * @param CatalogProductView $catalogProductView
     *
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CatalogEventEntity $catalogEvent,
        CatalogCategoryView $catalogCategoryView,
        CatalogProductSimple $catalogProductSimple,
        CatalogProductView $catalogProductView
    ) {
        $this->catalogCategoryView = $catalogCategoryView;
        $this->cmsIndex = $cmsIndex;
        $this->catalogProductView = $catalogProductView;

        $this->categoryName = $catalogProductSimple->getCategoryIds()[1];
        $this->productName = $catalogProductSimple->getName();

        $pageEvent = $catalogEvent->getDisplayState();
        if ($pageEvent['category_page'] == "Yes") {
            $this->checkEventBlockOnCategoryPagePresent();
        } else {
            $this->checkEventBlockOnCategoryPageAbsent();
        }
        if ($pageEvent['product_page'] == "Yes") {
            $this->checkEventBlockOnProductPagePresent();
        } else {
            $this->checkEventBlockOnProductPageAbsent();
        }
    }

    /**
     * Event block is visible on Category page
     *
     * @return void
     */
    protected function checkEventBlockOnCategoryPagePresent()
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenuBlock()->selectCategoryByName($this->categoryName);
        \PHPUnit_Framework_Assert::assertTrue(
            $this->catalogCategoryView->getEventBlock()->isVisible(),
            "EventBlock is absent on Category page"
        );
    }

    /**
     * Event block is invisible on Category page
     *
     * @return void
     */
    protected function checkEventBlockOnCategoryPageAbsent()
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenuBlock()->selectCategoryByName($this->categoryName);
        \PHPUnit_Framework_Assert::assertFalse(
            $this->catalogCategoryView->getEventBlock()->isVisible(),
            "EventBlock is present on Category page"
        );
    }

    /**
     * Event block is visible on Product page
     *
     * @return void
     */
    protected function checkEventBlockOnProductPagePresent()
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenuBlock()->selectCategoryByName($this->categoryName);
        $this->catalogCategoryView->getListProductBlock()->openProductViewPage($this->productName);
        \PHPUnit_Framework_Assert::assertTrue(
            $this->catalogProductView->getEventBlock()->isVisible(),
            "EventBlock is absent on Product page"
        );
    }

    /**
     * Event block is invisible on Product page
     *
     * @return void
     */
    protected function checkEventBlockOnProductPageAbsent()
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenuBlock()->selectCategoryByName($this->categoryName);
        $this->catalogCategoryView->getListProductBlock()->openProductViewPage($this->productName);
        \PHPUnit_Framework_Assert::assertFalse(
            $this->catalogProductView->getEventBlock()->isVisible(),
            "EventBlock is present on Product page"
        );
    }

    /**
     * Text visible/invisible Event block on page according to fixture(catalog page/product pages)
     *
     * @return string
     */
    public function toString()
    {
        return 'Event block is visible/invisible on catalog/product pages according to fixture';
    }
}

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
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\CatalogEvent\Test\Fixture\CatalogEventEntity;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Class AssertCatalogEventBlockVisible
 *
 * @package Magento\CatalogEvent\Test\Constraint
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
     * Constraint severeness
     *
     * @var string
     */
    public $pageDisplayState;

    /**
     * Constraint severeness
     *
     * @var CatalogCategoryView $catalogCategoryView
     */
    protected  $catalogCategoryView;

    /**
     * Constraint severeness
     *
     * @var CmsIndex $cmsIndex
     */
    protected  $cmsIndex;

    /**
     * Constraint severeness
     *
     * @var CatalogProductSimple $catalogProductSimple
     */
    protected  $catalogProductSimple;

    /**
     * Constraint severeness
     *
     * @var CatalogProductView $catalogProductView
     */
    protected $catalogProductView;

    protected $categoryName;

    protected $productName;

    /**
     * Assert that Event block is visible/invisible on page according to fixture(catalog page/product pages)
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogEventEntity $catalogEvent
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogProductSimple $catalogProductSimple
     * @param CatalogProductView $catalogProductView
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
        $this->catalogProductSimple = $catalogProductSimple;
        $this->catalogProductView = $catalogProductView;

        $this->categoryName = $this->catalogProductSimple->getDataFieldConfig('category_ids')['fixture']->getCategory()[0]->getName();
        $this->productName = $this->catalogProductSimple->getData('name');

        $pageEvent = $catalogEvent->getDisplayState();
        if($pageEvent['category_page'] == "Yes") {
            $this->blockEventOnCategoryPage();
        } else {
            $this->blockEventOnCategoryPageAbsent();
        }
        if($pageEvent['product_page'] == "Yes") {
            $this->blockEventOnProductPage();
        } else {
            $this->blockEventOnProductPageAbsent();
        }
    }

    /**
     * Event block is visible on Category page
     */
    protected function blockEventOnCategoryPage()
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenuBlock()->selectCategoryByName($this->categoryName);
        \PHPUnit_Framework_Assert::assertTrue(
            $this->catalogCategoryView->getEventBlock()->getEventBlock(),
            "EventBlock is absent on Category page"
        );
    }

    /**
     * Event block is invisible on Category page
     */
    protected function blockEventOnCategoryPageAbsent()
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenuBlock()->selectCategoryByName($this->categoryName);
        \PHPUnit_Framework_Assert::assertFalse(
            $this->catalogCategoryView->getEventBlock()->getEventBlock(),
            "EventBlock is present on Category page"
        );
    }

    /**
     * Event block is visible on Product page
     */
    protected function blockEventOnProductPage()
    {
        $this->cmsIndex->open();
        $this->catalogProductSimple->getDataFieldConfig('category_ids');
        $this->cmsIndex->getTopmenuBlock()->selectCategoryByName($this->categoryName);
        $this->catalogCategoryView->getListProductBlock()->openProductViewPage($this->productName);
        \PHPUnit_Framework_Assert::assertTrue(
            $this->catalogProductView->getEventBlock()->getEventBlock(),
            "EventBlock is absent on Product page"
        );
    }

    /**
     * Event block is invisible on Product page
     */
    protected function blockEventOnProductPageAbsent()
    {
        $this->cmsIndex->open();
        $this->catalogProductSimple->getDataFieldConfig('category_ids');
        $this->cmsIndex->getTopmenuBlock()->selectCategoryByName($this->categoryName);
        $this->catalogCategoryView->getListProductBlock()->openProductViewPage($this->productName);
        \PHPUnit_Framework_Assert::assertFalse(
            $this->catalogProductView->getEventBlock()->getEventBlock(),
            "EventBlock is present on Product page"
        );
    }

    /**
     * Text success visible/invisible Event Block on page according to fixture(catalog page/product pages)
     *
     * @return string
     */
    public function toString()
    {
        return 'Event block is visible/invisible on page according to fixture';
    }
}

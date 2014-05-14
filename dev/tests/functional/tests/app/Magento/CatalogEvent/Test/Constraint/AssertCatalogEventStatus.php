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
use Magento\CatalogEvent\Test\Page\Product\CatalogProductView;
use Magento\CatalogEvent\Test\Fixture\CatalogEventEntity;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\CatalogEvent\Test\Page\Category\CatalogCategoryView;

/**
 * Class AssertCatalogEventStatus
 * Check event status on category/product pages
 */
abstract class AssertCatalogEventStatus extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Catalog Event status
     *
     * @var string
     */
    protected $eventStatus = '';

    /**
     * Category Page on Frontend
     *
     * @var CatalogCategoryView
     */
    protected $catalogCategoryView;

    /**
     * Index Page on Frontend
     *
     * @var CmsIndex $cmsIndex
     */
    protected $cmsIndex;

    /**
     * Product simple fixture
     *
     * @var CatalogProductSimple
     */
    protected $catalogProductSimple;

    /**
     * Product Page on Frontend
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Assert that Event block has $eventStatus
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogEventEntity $catalogEvent
     * @param CatalogProductSimple $catalogProductSimple
     * @param CatalogProductView $catalogProductView
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        CatalogEventEntity $catalogEvent,
        CatalogProductSimple $catalogProductSimple,
        CatalogProductView $catalogProductView
    ) {
        $this->catalogCategoryView = $catalogCategoryView;
        $this->cmsIndex = $cmsIndex;
        $this->catalogProductSimple = $catalogProductSimple;
        $this->catalogProductView = $catalogProductView;

        $pageEvent = $catalogEvent->getDisplayState();
        if ($pageEvent['category_page'] == "Yes") {
            $this->checkEventStatusOnCategoryPage();
        }
        if ($pageEvent['product_page'] == "Yes") {
            $this->checkEventStatusOnProductPage();
        }
    }

    /**
     * Event block has $this->eventStatus on Category Page
     *
     * @return void
     */
    protected function checkEventStatusOnCategoryPage()
    {
        $categoryName = $this->catalogProductSimple->getCategoryIds()[0]['name'];
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenu()->selectCategoryByName($categoryName);
        \PHPUnit_Framework_Assert::assertEquals(
            $this->eventStatus,
            $this->catalogCategoryView->getEventBlock()->getEventStatus(),
            'Wrong event status is displayed.'
            . "\nExpected: " . $this->eventStatus
            . "\nActual: " . $this->catalogCategoryView->getEventBlock()->getEventStatus()
        );
    }

    /**
     * Event block has $this->eventStatus on Product Page
     *
     * @return void
     */
    protected function checkEventStatusOnProductPage()
    {
        $categoryName = $this->catalogProductSimple->getCategoryIds()[0]['name'];
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenu()->selectCategoryByName($categoryName);
        $this->catalogCategoryView->getListProductBlock()->openProductViewPage($this->catalogProductSimple->getName());
        \PHPUnit_Framework_Assert::assertEquals(
            $this->eventStatus,
            $this->catalogProductView->getEventBlock()->getEventStatus(),
            'Wrong event status is displayed.'
            . "\nExpected: " . $this->eventStatus
            . "\nActual: " . $this->catalogProductView->getEventBlock()->getEventStatus()
        );
    }

    /**
     * Text '$this->eventStatus' status present on the category/product pages
     *
     * @return string
     */
    public function toString()
    {
        return "$this->eventStatus status is present.";
    }
}

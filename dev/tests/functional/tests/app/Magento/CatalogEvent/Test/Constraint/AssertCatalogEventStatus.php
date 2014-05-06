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
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\CatalogEvent\Test\Fixture\CatalogEventEntity;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Class AssertCatalogEventStatus
 *
 * @package Magento\CatalogEvent\Test\Constraint
 */
abstract class AssertCatalogEventStatus extends AbstractConstraint
{
    /**
     * Event Status
     */
    protected $eventStatus = '';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Category Page on Frontend
     *
     * @var CatalogCategoryView $catalogCategoryView
     */
    protected $catalogCategoryView;

    /**
     * Index Page on Frontend
     *
     * @var CmsIndex $cmsIndex
     */
    protected $cmsIndex;

    /**
     * Product Page
     *
     * @var CatalogProductSimple $catalogProductSimple
     */
    protected $catalogProductSimple;

    /**
     * Product Page on Frontend
     *
     * @var CatalogProductView $catalogProductView
     */
    protected $catalogProductView;

    /**
     * Assert that Event block has $eventStatus
     *
     * @param CatalogCategoryView $catalogCategoryView
     * @param CmsIndex $cmsIndex
     * @param CatalogEventEntity $catalogEvent
     * @param CatalogProductSimple $catalogProductSimple
     * @param CatalogProductView $catalogProductView
     */
    public function processAssert(
        CatalogCategoryView $catalogCategoryView,
        CmsIndex $cmsIndex,
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
            $this->blockEventOnCategoryPage();
        }
        if ($pageEvent['product_page'] == "Yes") {
            $this->blockEventOnProductPage();
        }
    }

    /**
     * Event block has $eventStatus on Category Page
     * @return void
     */
    public function blockEventOnCategoryPage()
    {
        $categoryName = $this->catalogProductSimple->getDataFieldConfig('category_ids')['fixture']
            ->getCategory()[0]->getName();

        $this->cmsIndex->open();
        $this->catalogProductSimple->getDataFieldConfig('category_ids');
        $this->cmsIndex->getTopmenuBlock()->selectCategoryByName($categoryName);
        $actualMessage = $this->catalogCategoryView->getEventBlock()->getEventMessage();
        \PHPUnit_Framework_Assert::assertEquals(
            $this->eventStatus,
            $actualMessage,
            'Wrong event status message is displayed.'
            . "\nExpected: " . $this->eventStatus
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Event block has $eventStatus on Product Page
     * @return void
     */
    public function blockEventOnProductPage()
    {
        $categoryName = $this->catalogProductSimple->getDataFieldConfig('category_ids')['fixture']
            ->getCategory()[0]->getName();

        $this->cmsIndex->open();
        $this->catalogProductSimple->getDataFieldConfig('category_ids');
        $this->cmsIndex->getTopmenuBlock()->selectCategoryByName($categoryName);

        $productName = $this->catalogProductSimple->getData('name');
        $this->catalogCategoryView->getListProductBlock()->openProductViewPage($productName);
        $actualMessage = $this->catalogProductView->getEventBlock()->getEventMessage();
        \PHPUnit_Framework_Assert::assertEquals(
            $this->eventStatus,
            $actualMessage,
            'Wrong event status message is displayed.'
            . "\nExpected: " . $this->eventStatus
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Text success present '$eventStatus' message
     *
     * @return string
     */
    public function toString()
    {
        return "$this->eventStatus message is present.";
    }
}

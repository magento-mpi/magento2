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
 * Class AssertCatalogEventIsUpcoming
 *
 * @package Magento\CatalogEvent\Test\Constraint
 */
class AssertCatalogEventIsUpcoming extends AbstractConstraint
{
    const EVENT_STATUS = 'Coming Soon';
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

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
    protected $cmsIndex;

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

    /**
     * Assert that Event block has upcoming status
     *
     * @param CatalogCategoryView $catalogCategoryView
     * @param CmsIndex $cmsIndex
     * @param CatalogEventEntity $catalogEvent
     * @param CatalogProductSimple $catalogProductSimple
     * @param CatalogProductView $catalogProductView
     * @return void
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
        if($pageEvent['category_page'] == "Yes") {
            $this->blockEventOnCategoryPage();
        }
        if($pageEvent['product_page'] == "Yes") {
            $this->blockEventOnProductPage();
        }
    }

    /**
     * Event block has upcoming status on Category Page
    */
    protected function blockEventOnCategoryPage()
    {
        $categoryName = $this->catalogProductSimple->getDataFieldConfig('category_ids')['fixture']->getCategory()[0]->getName();

        $this->cmsIndex->open();
        $this->catalogProductSimple->getDataFieldConfig('category_ids');
        $this->cmsIndex->getTopmenuBlock()->selectCategoryByName($categoryName);
        $actualMessage = $this->catalogCategoryView->getEventBlock()->getEventMessage();
        \PHPUnit_Framework_Assert::assertEquals(
            self::EVENT_STATUS,
            $actualMessage,
            'Wrong event status message is displayed.'
            . "\nExpected: " . self::EVENT_STATUS
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Event block has upcoming status on Product Page
     */
    protected function blockEventOnProductPage()
    {
        $categoryName = $this->catalogProductSimple->getDataFieldConfig('category_ids')['fixture']->getCategory()[0]->getName();

        $this->cmsIndex->open();
        $this->catalogProductSimple->getDataFieldConfig('category_ids');
        $this->cmsIndex->getTopmenuBlock()->selectCategoryByName($categoryName);

        $productName = $this->catalogProductSimple->getData('name');
        $this->catalogCategoryView->getListProductBlock()->openProductViewPage($productName);
        $actualMessage = $this->catalogProductView->getEventBlock()->getEventMessage();
        \PHPUnit_Framework_Assert::assertEquals(
            self::EVENT_STATUS,
            $actualMessage,
            'Wrong event status message is displayed.'
            . "\nExpected: " . self::EVENT_STATUS
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Text success present 'Coming Soon' message
     *
     * @return string
     */
    public function toString()
    {
        return 'Coming Soon message is present.';
    }

}
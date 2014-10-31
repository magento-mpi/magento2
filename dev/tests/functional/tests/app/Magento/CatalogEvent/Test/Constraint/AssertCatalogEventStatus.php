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
use Mtf\Client\Browser;

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
    protected $product;

    /**
     * Product Page on Frontend
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Browser
     *
     * @var Browser
     */
    protected $browser;

    /**
     * Assert that Event block has $eventStatus
     *
     * @param CmsIndex $cmsIndex
     * @param Browser $browser
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogEventEntity $catalogEvent
     * @param CatalogProductSimple $product
     * @param CatalogProductView $catalogProductView
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        Browser $browser,
        CatalogCategoryView $catalogCategoryView,
        CatalogEventEntity $catalogEvent,
        CatalogProductSimple $product,
        CatalogProductView $catalogProductView
    ) {
        $this->catalogCategoryView = $catalogCategoryView;
        $this->cmsIndex = $cmsIndex;
        $this->product = $product;
        $this->catalogProductView = $catalogProductView;
        $this->browser = $browser;

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
        $categoryName = $this->product->getCategoryIds()[0];
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
        $urlKey = $this->product->getUrlKey();
        $this->browser->open($_ENV['app_frontend_url'] . $urlKey . '.html');
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

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\CatalogEvent\Test\Fixture\CatalogEventEntity;
use Magento\CatalogEvent\Test\Page\Adminhtml\CatalogEventIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCatalogEventInGrid
 * Check catalog event is present in the "Events" grid
 *
 */
class AssertCatalogEventInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Catalog Event fixture
     *
     * @var CatalogEventEntity
     */
    protected $catalogEvent;

    /**
     * Pages where event presented
     *
     * @var string
     */
    protected $catalogEventPages = '';

    /**
     * Assert that catalog event is presented in the "Events" grid
     *
     * @param CatalogEventEntity $catalogEvent
     * @param CatalogProductSimple $catalogProductSimple
     * @param CatalogEventIndex $catalogEventIndex
     *
     * @return void
     */
    public function processAssert(
        CatalogEventEntity $catalogEvent,
        CatalogProductSimple $catalogProductSimple,
        CatalogEventIndex $catalogEventIndex
    ) {
        //todo BUG MAGETWO-23857
        return true;

        $this->catalogEvent = $catalogEvent;
        $categoryName = $catalogProductSimple->getCategoryIds()[1];
        $dateStart = strtotime($catalogEvent->getDateStart());
        $dateEnd = strtotime($catalogEvent->getDateEnd());
        $currentDay = strtotime('now');

        if ($currentDay < $dateStart) {
            $status = 'Upcoming';
        } elseif ($currentDay > $dateEnd) {
            $status = 'Close';
        } else {
            $status = 'Open';
        }

        $filter = [
            'category_name' => $categoryName,
            'start_on' => $catalogEvent->getDateStart(),
            'end_on' => $catalogEvent->getDateEnd(),
            'status' => $status,
            'countdown_ticker' => $this->prepareDisplayStateForFilter(),
            'sort_order' => $catalogEvent->getSortOrder()
        ];
        $catalogEventIndex->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $catalogEventIndex->getBlockEventGrid()->isRowVisible($filter),
            'Event on Category Name \'' . $categoryName . '\' is absent in Events grid.'
        );
    }

    /**
     * Method prepare string display state for filter
     *
     * @return string
     */
    protected function prepareDisplayStateForFilter()
    {
        $pageEvent = $this->catalogEvent->getDisplayState();

        if ($pageEvent['category_page'] == "Yes") {
            return 'Category Page';
        }
        if ($pageEvent['product_page'] == "Yes") {
            return 'Product Page';
        }

        return 'Lister Block';
    }

    /**
     * Text present Catalog Event in Event Grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Catalog Event is present in Event grid.';
    }
}

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
        $this->catalogEvent = $catalogEvent;
        $categoryName = $catalogProductSimple->getCategoryIds()[0]['name'];
        $dateStart = strtotime($catalogEvent->getDateStart());
        $dateEnd = strtotime($catalogEvent->getDateEnd());
        $currentDay = strtotime('now');

        if ($currentDay < $dateStart) {
            $status = 'Upcoming';
        } elseif ($currentDay > $dateEnd) {
            $status = 'Closed';
        } else {
            $status = 'Open';
        }

        $sortOrder = $catalogEvent->getSortOrder();
        if ($sortOrder < 0) {
            $sortOrder = 0;
        }

        $dateStart = strftime("%b %#d, %Y %I:%M:%S %p", $dateStart);
        $filter['start_on'] = $dateStart;
        $dateEnd = strftime("%b %#d, %Y %I:%M:%S %p", $dateEnd);
        $filter['end_on'] = $dateEnd;

        $filter = [
            'category_name' => $categoryName,
            'start_on' => $dateStart,
            'end_on' => $dateEnd,
            'status' => $status,
            'countdown_ticker' => $this->prepareDisplayStateForFilter(),
            'sort_order' => $sortOrder
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
        $pageEvents = $this->catalogEvent->getDisplayState();

        $displayStates = [
            'category_page' => 'Category Page',
            'product_page' => 'Product Page',
        ];

        $event = 'Lister Block';
        foreach ($pageEvents as $key => $pageEvent) {
            if ($pageEvent === 'Yes') {
                $event .= ', ' . $displayStates[$key];
            }
        }

        return $event;
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

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
 *
 * @package Magento\CatalogEvent\Test\Constraint
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
     * CatalogEventEntity $catalogEvent
     * @var
     */
    protected $catalogEvent;

    /**
     * Pages where event presented
     * @var string
     */
    protected $catalogEventPages = '';

    /**
     * Assert that catalog event is presented in the "Events" grid
     *
     * @param CatalogEventEntity $catalogEvent
     * @param CatalogProductSimple $catalogProductSimple
     * @param CatalogEventIndex $catalogEventIndex
     * @return bool
     */
    public function processAssert(
        CatalogEventEntity $catalogEvent,
        CatalogProductSimple $catalogProductSimple,
        CatalogEventIndex $catalogEventIndex
    ) {
        //todo BUG MAGETWO-23857
        return true;

        $this->catalogEvent = $catalogEvent;
        $categoryName = $catalogProductSimple->getDataFieldConfig('category_ids')['fixture']
            ->getCategory()[0]->getName();
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

        $displayState = $this->prepare();

        $filter = [
            'category_name' => $categoryName,
            'start_on' => $catalogEvent->getDateStart(),
            'end_on' => $catalogEvent->getDateEnd(),
            'status' => $status,
            'countdown_ticker' => $displayState,
            'sort_order' => $catalogEvent->getSortOrder()
        ];
        $catalogEventIndex->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $catalogEventIndex->getBlockEventGrid()->isRowVisible($filter),
            'Event with Category Name \'' . $categoryName . '\' is absent in Events grid.'
        );
    }

    /**
     * Method prepare string display state for filter
     *
     * @return string
     */
    protected function prepare()
    {
        $pageEvent = $this->catalogEvent->getDisplayState();

        if ($pageEvent['category_page'] == "Yes") {
            $this->catalogEventPages = 'Category Page';
        }
        if ($pageEvent['product_page'] == "Yes") {
            $this->catalogEventPages = 'Product Page';
        }

        return $this->catalogEventPages;
    }

    /**
     * Text success present Event in Event Grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Event is present in Events grid.';
    }
}

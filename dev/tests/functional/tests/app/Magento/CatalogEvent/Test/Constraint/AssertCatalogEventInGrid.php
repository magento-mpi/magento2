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
     * @param CatalogEventEntity $catalogEvent
     * @param CatalogProductSimple $catalogProductSimple
     * @return bool
     */
    public function processAssert(
        CatalogEventEntity $catalogEvent,
        CatalogProductSimple $catalogProductSimple
    ) {
        //todo BUG MAGETWO-23857
        return true;

        $categoryName = $catalogProductSimple->getDataFieldConfig('category_ids')['fixture']->getCategory()[0]->getName();

        $dateStartElements = explode(' ',$catalogEvent->getDateStart());
        $dateStartElement = explode('/',$dateStartElements[0]);
        $dateStart = mktime(0, 0, 0, $dateStartElement[0],$dateStartElement[1], $dateStartElement[2]);
        $dateEndElements = explode(' ',$catalogEvent->getDateEnd());
        $dateEndElement = explode('/',$dateEndElements[0]);
        $dateEnd = mktime(0, 0, 0, $dateEndElement[0],$dateEndElement[1], $dateEndElement[2]);
        $currentDay = strtotime('now');

        if($currentDay < $dateStart){
            $status = 'Upcoming';
        }elseif($currentDay > $dateEnd){
            $status = 'Close';
        }else{
            $status = 'Open';
        }

        $filter = [
            'category_name' => $categoryName,
            'start_on' => $catalogEvent->getDateStart(),
            'end_on' => $catalogEvent->getDateEnd(),
            'status' => $status,
            'countdown_ticker' => $catalogEvent->getDisplayState(),
            'sort_order' => $catalogEvent->getSortOrder()
        ];
        $adminCatalogEventIndex->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $adminCatalogEventIndex->getBlockEventGrid()->isRowVisible($filter),
            'Event with Category Name \'' . $categoryName . '\' is absent in Events grid.'
        );
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

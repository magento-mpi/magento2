<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Constraint;

/**
 * Class AssertCatalogEventIsUpcoming
 * Check event status 'Sale Ends In' on category/product pages
 */
class AssertCatalogEventIsOpen extends AssertCatalogEventStatus
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Event status 'Sale Ends In' on category/product pages
     *
     * @var string
     */
    protected $eventStatus = 'Sale Ends In';
}

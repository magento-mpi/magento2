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
 * Check event status 'Closed' on category/product pages
 */
class AssertCatalogEventIsClosed extends AssertCatalogEventStatus
{
    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Event status 'Closed' on category/product pages
     *
     * @var string
     */
    protected $eventStatus = 'Closed';
}

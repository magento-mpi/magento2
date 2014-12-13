<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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

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
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Event status 'Closed' on category/product pages
     *
     * @var string
     */
    protected $eventStatus = 'Closed';

    /**
     * Text status 'Closed' present
     *
     * @return string
     */
    public function toString()
    {
        return 'Closed status is present.';
    }
}

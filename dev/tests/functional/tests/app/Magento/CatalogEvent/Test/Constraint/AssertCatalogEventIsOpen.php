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
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Event status 'Sale Ends In' on category/product pages
     *
     * @var string
     */
    protected $eventStatus = 'Sale Ends In';

    /**
     * Text status 'Sale Ends In' present
     *
     * @return string
     */
    public function toString()
    {
        return 'Sale Ends In status is present.';
    }
}

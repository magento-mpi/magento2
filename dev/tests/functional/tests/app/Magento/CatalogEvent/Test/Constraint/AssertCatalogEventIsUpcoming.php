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
 * Check event status 'Coming Soon' on category/product pages
 */
class AssertCatalogEventIsUpcoming extends AssertCatalogEventStatus
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Event status 'Coming Soon' on category/product pages
     *
     * @var string
     */
    protected $eventStatus = 'Coming Soon';
}

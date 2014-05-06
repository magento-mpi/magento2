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
 *
 * @package Magento\CatalogEvent\Test\Constraint
 */
class AssertCatalogEventIsUpcoming extends AssertCatalogEventStatus
{
    protected $eventStatus = 'Coming Soon';
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Text success present 'Coming Soon' message
     *
     * @return string
     */
    public function toString()
    {
        return 'Coming Soon message is present.';
    }
}
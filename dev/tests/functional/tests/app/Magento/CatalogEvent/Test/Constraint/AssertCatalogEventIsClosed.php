<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Constraint;

/**
 * Class AssertCatalogEventIsClosed
 *
 * @package Magento\CatalogEvent\Test\Constraint
 */
class AssertCatalogEventIsClosed extends AssertCatalogEventStatus
{
    protected $eventStatus = 'Closed';
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Text success present 'Closed' message
     *
     * @return string
     */
    public function toString()
    {
        return 'Closed message is present.';
    }
}

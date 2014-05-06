<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Constraint;

/**
 * Class AssertCatalogEventIsOpen
 *
 * @package Magento\CatalogEvent\Test\Constraint
 */
class AssertCatalogEventIsOpen extends AssertCatalogEventStatus
{
    protected $eventStatus = 'Sale Ends In';
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Text success present 'Sale Ends In' message
     *
     * @return string
     */
    public function toString()
    {
        return 'Sale Ends In message is present.';
    }
}

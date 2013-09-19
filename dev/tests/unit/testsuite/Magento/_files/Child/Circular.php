<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\Test\Di\Child;

class Circular extends \Magento\Test\Di\Child
{
    /**
     * @param \Magento\Test\Di\Aggregate\AggregateParent $aggregateParent
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(\Magento\Test\Di\Aggregate\AggregateParent $aggregateParent)
    {

    }
}

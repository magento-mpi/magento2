<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Di\Aggregate;

class AggregateParent implements \Magento\Test\Di\Aggregate\AggregateInterface
{
    public $interface;

    public $parent;

    public $child;

    public $scalar;

    public $optionalScalar;

    public function __construct(
        \Magento\Test\Di\DiInterface $interface,
        \Magento\Test\Di\DiParent $parent,
        \Magento\Test\Di\Child $child,
        $scalar,
        $optionalScalar = 1
    ) {
        $this->interface = $interface;
        $this->parent = $parent;
        $this->child = $child;
        $this->scalar = $scalar;
        $this->optionalScalar = $optionalScalar;
    }
}

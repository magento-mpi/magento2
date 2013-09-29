<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Di\Aggregate;

class Child extends \Magento\Test\Di\Aggregate\AggregateParent
{
    public $secondScalar;

    public $secondOptionalScalar;

    public function __construct(
        \Magento\Test\Di\DiInterface $interface,
        \Magento\Test\Di\DiParent $parent,
        \Magento\Test\Di\Child $child,
        $scalar,
        $secondScalar,
        $optionalScalar = 1,
        $secondOptionalScalar = ''
    ) {
        parent::__construct($interface, $parent, $child, $scalar, $optionalScalar);
        $this->secondScalar = $secondScalar;
        $this->secondOptionalScalar = $secondOptionalScalar;
    }

}

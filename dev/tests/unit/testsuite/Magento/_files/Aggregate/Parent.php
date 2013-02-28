<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Test_Di_Aggregate_Parent implements Magento_Test_Di_Aggregate_Interface
{
    public $interface;
    public $parent;
    public $child;
    public $scalar;
    public $optionalScalar;

    public function __construct (
        Magento_Test_Di_Interface $interface,
        Magento_Test_Di_Parent $parent,
        Magento_Test_Di_Child $child,
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

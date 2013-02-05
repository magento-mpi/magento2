<?php

class Magento_Test_Di_Aggregate_Child extends Magento_Test_Di_Aggregate_Parent
{
    public $secondScalar;

    public $secondOptionalScalar;

    public function __construct(
        Magento_Test_Di_Interface $interface,
        Magento_Test_Di_Parent $parent,
        Magento_Test_Di_Child $child,
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

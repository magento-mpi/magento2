<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Test_Di_Aggregate_WithOptional
{
    public $parent;

    public $child;

    public function __construct(Magento_Test_Di_Parent $parent = null, Magento_Test_Di_Child $child = null)
    {
        $this->parent = $parent;
        $this->child  = $child;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_Test_Di_Child_Circular extends Magento_Test_Di_Child
{
    /**
     * @param Magento_Test_Di_Aggregate_Parent $aggregateParent
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(Magento_Test_Di_Aggregate_Parent $aggregateParent)
    {

    }
}

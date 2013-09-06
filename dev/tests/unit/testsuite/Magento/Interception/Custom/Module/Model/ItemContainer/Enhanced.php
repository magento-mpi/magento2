<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Interception_Custom_Module_Model_ItemContainer_Enhanced extends
Magento_Interception_Custom_Module_Model_ItemContainer
{
    /**
     * @return string
     */
    public function getName()
    {
        return parent::getName() . '_enhanced';
    }
}

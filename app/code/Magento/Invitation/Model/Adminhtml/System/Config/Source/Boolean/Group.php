<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation source for reffered customer group system configuration
 */
class Magento_Invitation_Model_Adminhtml_System_Config_Source_Boolean_Group
    implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            1 => __('Same as Inviter'),
            0 => __('Default Customer Group from System Configuration')
        );
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation source for reffered customer group system configuration
 */
class Enterprise_Invitation_Model_Adminhtml_System_Config_Source_Boolean_Group
{
    public function toOptionArray()
    {
        return array(
            1 => Mage::helper('Enterprise_Invitation_Helper_Data')->__('Same as Inviter'),
            0 => Mage::helper('Enterprise_Invitation_Helper_Data')->__('Default Customer Group from System Configuration')
        );
    }
}

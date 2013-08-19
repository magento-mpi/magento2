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
            1 => __('Same as Inviter'),
            0 => __('Default Customer Group from System Configuration')
        );
    }
}

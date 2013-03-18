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
 * Invitation config source for customer registration field
 */
class Enterprise_Invitation_Model_Adminhtml_System_Config_Source_Boolean_Registration
{
    public function toOptionArray()
    {
        return array(
            1 => Mage::helper('Enterprise_Invitation_Helper_Data')->__('By Invitation Only'),
            0 => Mage::helper('Enterprise_Invitation_Helper_Data')->__('Available to All')
        );
    }
}

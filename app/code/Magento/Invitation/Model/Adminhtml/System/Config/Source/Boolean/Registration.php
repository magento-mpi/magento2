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
 * Invitation config source for customer registration field
 */
class Magento_Invitation_Model_Adminhtml_System_Config_Source_Boolean_Registration
{
    public function toOptionArray()
    {
        return array(
            1 => __('By Invitation Only'),
            0 => __('Available to All')
        );
    }
}

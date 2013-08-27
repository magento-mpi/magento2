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
            1 => __('By Invitation Only'),
            0 => __('Available to All')
        );
    }
}

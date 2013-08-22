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
 * Invitation status option source
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Model_Source_Invitation_Options
    implements Magento_Core_Model_Option_ArrayInterface

{
    /**
     * Return list of invitation statuses as options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return Mage::getSingleton('Enterprise_Invitation_Model_Source_Invitation_Status')->getOptions();

    }
}

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
class Enterprise_Invitation_Model_Source_Invitation_GroupId
    implements Mage_Core_Model_Option_ArrayInterface

{
    /**
     * Return list of invitation statuses as options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return Mage::getModel('Mage_Customer_Model_Group')->getCollection()
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

    }


}

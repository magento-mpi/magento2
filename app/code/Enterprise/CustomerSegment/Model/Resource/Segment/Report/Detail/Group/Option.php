<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_CustomerSegment_Model_Resource_Segment_Report_Detail_Group_Option
    implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return Mage::getResourceModel('Mage_Customer_Model_Resource_Group_Collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();
    }
}

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
     * @var Mage_Customer_Model_Resource_Group_Collection
     */
    protected $_resourceCollection;

    /**
     * @param Mage_Customer_Model_Resource_Group_Collection $groupCollection
     */
    public function __construct(Mage_Customer_Model_Resource_Group_Collection $groupCollection)
    {
        $this->_resourceCollection = $groupCollection;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_resourceCollection
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_CustomerSegment_Model_Resource_Segment_Report_Collection
    extends Enterprise_CustomerSegment_Model_Resource_Segment_Collection
{
    /**
     * @return Enterprise_CustomerSegment_Model_Resource_Segment_Report_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addCustomerCountToSelect()
            ->addWebsitesToResult();
        return $this;
    }
}

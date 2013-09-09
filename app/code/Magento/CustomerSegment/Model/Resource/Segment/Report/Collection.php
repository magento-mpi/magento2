<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_CustomerSegment_Model_Resource_Segment_Report_Collection
    extends Magento_CustomerSegment_Model_Resource_Segment_Collection
{
    /**
     * @return Magento_CustomerSegment_Model_Resource_Segment_Report_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addCustomerCountToSelect()
            ->addWebsitesToResult();
        return $this;
    }
}

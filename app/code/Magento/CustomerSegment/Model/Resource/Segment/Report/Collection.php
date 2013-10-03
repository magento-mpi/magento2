<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Model\Resource\Segment\Report;

class Collection
    extends \Magento\CustomerSegment\Model\Resource\Segment\Collection
{
    /**
     * @return \Magento\CustomerSegment\Model\Resource\Segment\Report\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addCustomerCountToSelect()
            ->addWebsitesToResult();
        return $this;
    }
}

<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerSegment\Model\Resource\Segment\Report;

class Collection extends \Magento\CustomerSegment\Model\Resource\Segment\Collection
{
    /**
     * @return \Magento\CustomerSegment\Model\Resource\Segment\Report\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addCustomerCountToSelect()->addWebsitesToResult();
        return $this;
    }
}

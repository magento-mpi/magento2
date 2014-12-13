<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerSegment\Model\Resource\Segment\Report\Detail\Group;

class Option implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Customer\Model\Resource\Group\Collection
     */
    protected $_resourceCollection;

    /**
     * @param \Magento\Customer\Model\Resource\Group\Collection $groupCollection
     */
    public function __construct(\Magento\Customer\Model\Resource\Group\Collection $groupCollection)
    {
        $this->_resourceCollection = $groupCollection;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_resourceCollection->addFieldToFilter(
            'customer_group_id',
            ['gt' => 0]
        )->load()->toOptionHash();
    }
}

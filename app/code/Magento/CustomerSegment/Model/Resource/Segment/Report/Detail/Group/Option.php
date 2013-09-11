<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Model\Resource\Segment\Report\Detail\Group;

class Option
    implements \Magento\Core\Model\Option\ArrayInterface
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
        return $this->_resourceCollection
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();
    }
}

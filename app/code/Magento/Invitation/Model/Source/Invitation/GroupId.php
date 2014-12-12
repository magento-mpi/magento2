<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Invitation\Model\Source\Invitation;

use Magento\Customer\Api\GroupManagementInterface as CustomerGroupManagement;

/**
 * Invitation group id options source
 */
class GroupId implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var CustomerGroupManagement
     */
    protected $customerGroupManagement;

    /**
     * @var \Magento\Framework\Convert\Object
     */
    protected $_objectConverter;

    /**
     * @param CustomerGroupManagement $customerGroupManagement
     * @param \Magento\Framework\Convert\Object $objectConverter
     */
    public function __construct(
        CustomerGroupManagement $customerGroupManagement,
        \Magento\Framework\Convert\Object $objectConverter
    ) {
        $this->customerGroupManagement = $customerGroupManagement;
        $this->_objectConverter = $objectConverter;
    }

    /**
     * Return list of groups.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_objectConverter->toOptionHash(
            $this->customerGroupManagement->getLoggedInGroups(),
            'id',
            'code'
        );
    }
}

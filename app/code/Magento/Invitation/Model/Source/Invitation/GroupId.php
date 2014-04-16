<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Invitation\Model\Source\Invitation;

/**
 * Invitation group id options source
 */
class GroupId implements \Magento\Option\ArrayInterface
{
    /**
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface
     */
    protected $_customerGroupService;

    /**
     * @var \Magento\Convert\Object
     */
    protected $_objectConverter;

    /**
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $customerGroupService
     * @param \Magento\Convert\Object $objectConverter
     */
    public function __construct(
        \Magento\Customer\Service\V1\CustomerGroupServiceInterface $customerGroupService,
        \Magento\Convert\Object $objectConverter
    ) {
        $this->_customerGroupService = $customerGroupService;
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
            $this->_customerGroupService->getGroups(false),
            'id',
            'code'
        );
    }
}

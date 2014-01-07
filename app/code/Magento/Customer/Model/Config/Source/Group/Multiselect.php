<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Config\Source\Group;

class Multiselect implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Customer groups options array
     *
     * @var null|array
     */
    protected $_options;

    /**
     * @var \Magento\Customer\Service\CustomerGroupV1Interface
     */
    protected $_groupService;

    /**
     * @var \Magento\Convert\Object
     */
    protected $_converter;

    /**
     * @param \Magento\Customer\Service\CustomerGroupV1Interface $groupService
     * @param \Magento\Convert\Object $converter
     */
    public function __construct(
        \Magento\Customer\Service\CustomerGroupV1Interface $groupService,
        \Magento\Convert\Object $converter
    ) {
        $this->_groupService = $groupService;
        $this->_converter = $converter;
    }

    /**
     * Retrieve customer groups as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $groups = $this->_groupService->getGroups(FALSE);
            $this->_options = $this->_converter->toOptionArray($groups, 'id', 'code');
        }
        return $this->_options;
    }
}

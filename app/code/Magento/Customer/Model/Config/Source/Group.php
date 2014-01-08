<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Customer\Model\Config\Source;

class Group implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $_options;

    /**
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface
     */
    protected $_groupService;

    /**
     * @var \Magento\Convert\Object
     */
    protected $_converter;

    /**
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService
     * @param \Magento\Convert\Object $converter
     */
    public function __construct(
        \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService,
        \Magento\Convert\Object $converter
    ) {
        $this->_groupService = $groupService;
        $this->_converter = $converter;
    }

    public function toOptionArray()
    {
        if (!$this->_options) {
            $groups = $this->_groupService->getGroups(FALSE);
            $this->_options = $this->_converter->toOptionArray($groups, 'id', 'code');
            array_unshift($this->_options, array('value'=> '', 'label'=> __('-- Please Select --')));
        }
        return $this->_options;
    }
}

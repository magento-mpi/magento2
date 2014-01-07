<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer group edit block
 */
namespace Magento\Customer\Block\Adminhtml\Group;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Customer\Service\CustomerGroupV1Interface
     */
    protected $_groupService = null;
    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Customer\Service\CustomerGroupV1Interface $groupService
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Customer\Service\CustomerGroupV1Interface $groupService,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_groupService = $groupService;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_group';
        $this->_blockGroup = 'Magento_Customer';

        $this->_updateButton('save', 'label', __('Save Customer Group'));
        $this->_updateButton('delete', 'label', __('Delete Customer Group'));

        /** @var \Magento\Customer\Service\Entity\V1\CustomerGroup $group */
        $group = $this->_coreRegistry->registry('current_group');
        if (!$group || !$group->getId() || !$this->_groupService->canDelete($group->getId())) {
            $this->_removeButton('delete');
        }
    }

    public function getHeaderText()
    {
        $currentGroup = $this->_coreRegistry->registry('current_group');
        if (!is_null($currentGroup->getId())) {
            return __('Edit Customer Group "%1"', $this->escapeHtml($currentGroup->getCustomerGroupCode()));
        } else {
            return __('New Customer Group');
        }
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-customer-groups';
    }
}

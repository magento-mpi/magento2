<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Group;

use Magento\Customer\Controller\Adminhtml\Group;

/**
 * Customer group edit block
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Customer Group Service
     *
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface
     */
    protected $_groupService = null;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_groupService = $groupService;
        parent::__construct($context, $data);
    }

    /**
     * Update Save and Delete buttons. Remove Delete button if group can't be deleted.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_group';
        $this->_blockGroup = 'Magento_Customer';

        $this->_updateButton('save', 'label', __('Save Customer Group'));
        $this->_updateButton('delete', 'label', __('Delete Customer Group'));

        $groupId = $this->_coreRegistry->registry(Group::REGISTRY_CURRENT_GROUP_ID);
        if (!$groupId || !$this->_groupService->canDelete($groupId)) {
            $this->_removeButton('delete');
        }
    }

    /**
     * Retrieve the header text, either editing an existing group or creating a new one.
     *
     * @return string
     */
    public function getHeaderText()
    {
        $groupId = $this->_coreRegistry->registry(Group::REGISTRY_CURRENT_GROUP_ID);
        if (is_null($groupId)) {
            return __('New Customer Group');
        } else {
            $group = $this->_groupService->getGroup($groupId);
            return __('Edit Customer Group "%1"', $this->escapeHtml($group->getCode()));
        }
    }

    /**
     * Retrieve CSS classes added to the header.
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'icon-head head-customer-groups';
    }
}

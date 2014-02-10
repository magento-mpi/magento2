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
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
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

        $group = $this->_coreRegistry->registry('current_group');
        if (!$group || !$group->getId() || $group->usesAsDefault()) {
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

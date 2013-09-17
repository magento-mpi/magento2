<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer group edit block
 */
class Magento_Adminhtml_Block_Customer_Group_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_controller = 'customer_group';

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

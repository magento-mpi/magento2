<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Block\Adminhtml\Types;

/**
 * Adminhtml Google Content Types Mapping form block
 */
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

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_blockGroup = 'Magento_GoogleShopping';
        $this->_controller = 'adminhtml_types';
        $this->_mode = 'edit';
        $model = $this->_coreRegistry->registry('current_item_type');
        $this->_removeButton('reset');
        $this->_updateButton('save', 'label', __('Save Mapping'));
        $this->_updateButton('save', 'id', 'save_button');
        $this->_updateButton('delete', 'label', __('Delete Mapping'));
        if ($model && !$model->getId()) {
            $this->_removeButton('delete');
        }
    }

    /**
     * Get init JavaScript for form
     *
     * @return string
     */
    public function getFormInitScripts()
    {
        return $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Template'
        )->setTemplate(
            'Magento_GoogleShopping::types/edit.phtml'
        )->toHtml();
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (!is_null($this->_coreRegistry->registry('current_item_type')->getId())) {
            return __('Edit attribute set mapping');
        } else {
            return __('New attribute set mapping');
        }
    }

    /**
     * Get css class name for header block
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'icon-head head-customer-groups';
    }
}

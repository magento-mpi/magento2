<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Google Content Types Mapping form block
 */

namespace Magento\GoogleShopping\Block\Adminhtml\Types;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

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
        return $this->getLayout()->createBlock('Magento\Core\Block\Template')
            ->setTemplate('Magento_GoogleShopping::types/edit.phtml')
            ->toHtml();
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

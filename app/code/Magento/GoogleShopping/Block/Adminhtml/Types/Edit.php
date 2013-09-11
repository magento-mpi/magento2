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
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\GoogleShopping\Block\Adminhtml\Types;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{
    protected function _construct()
    {
        parent::_construct();
        $this->_blockGroup = 'Magento_GoogleShopping';
        $this->_controller = 'adminhtml_types';
        $this->_mode = 'edit';
        $model = \Mage::registry('current_item_type');
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
        return $this->getLayout()->createBlock('\Magento\Core\Block\Template')
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
        if(!is_null(\Mage::registry('current_item_type')->getId())) {
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

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Attribute_Type_Select
    extends Magento_Adminhtml_Block_Widget_Form
{

    protected $_template = 'edit/type/select.phtml';

    /**
     * Preparing block layout
     */
    protected function _prepareLayout()
    {
        $this->addChild('add_select_row_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label' => __('Add New Option'),
            'class' => 'add add-select-row',
            'id'    => '{{prefix}}_add_select_row_button_{{id}}'
        ));

        $this->addChild('delete_select_row_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label' => __('Delete Option'),
            'class' => 'delete delete-select-row icon-btn'
        ));

        return parent::_prepareLayout();
    }

    /**
     * Retrieve add button html
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_select_row_button');
    }

    /**
     * Retrieve delete button html
     *
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_select_row_button');
    }
}

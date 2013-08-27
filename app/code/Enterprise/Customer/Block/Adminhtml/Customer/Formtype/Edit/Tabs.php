<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Fort Type Edit Tabs Block
 *
 * @category   Enterprise
 * @package    Enterprise_Customer
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Formtype_Edit_Tabs extends Magento_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize edit tabs
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('enterprise_customer_formtype_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Form Type Information'));
    }
}

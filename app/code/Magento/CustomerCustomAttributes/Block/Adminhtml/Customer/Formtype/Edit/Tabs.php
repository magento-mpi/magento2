<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Fort Type Edit Tabs Block
 *
 * @category   Magento
 * @package    Magento_CustomerCustomAttributes
 */
class Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Formtype_Edit_Tabs extends Magento_Adminhtml_Block_Widget_Tabs
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

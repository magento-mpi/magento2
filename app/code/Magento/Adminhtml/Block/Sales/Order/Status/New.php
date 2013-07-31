<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_Sales_Order_Status_New extends Magento_Adminhtml_Block_Widget_Form_Container
{

    protected function _construct()
    {
        $this->_objectId = 'status';
        $this->_controller = 'sales_order_status';
        $this->_mode = 'new';

        parent::_construct();
        $this->_updateButton('save', 'label', Mage::helper('Mage_Sales_Helper_Data')->__('Save Status'));
        $this->_removeButton('delete');
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('Mage_Sales_Helper_Data')->__('New Order Status');
    }
}

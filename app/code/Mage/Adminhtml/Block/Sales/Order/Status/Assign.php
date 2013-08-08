<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Sales_Order_Status_Assign extends Mage_Adminhtml_Block_Widget_Form_Container
{

    protected function _construct()
    {
        $this->_controller = 'sales_order_status';
        $this->_mode       = 'assign';
        parent::_construct();
        $this->_updateButton('save', 'label', __('Save Status Assignment'));
        $this->_removeButton('delete');
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Assign Order Status to State');
    }
}

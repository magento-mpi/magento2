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

    public function __construct()
    {
        $this->_controller = 'sales_order_status';
        $this->_mode       = 'assign';
        parent::__construct();
        $this->_updateButton('save', 'label', Mage::helper('Mage_Sales_Helper_Data')->__('Save BugsCoverage Assignment'));
        $this->_removeButton('delete');
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('Mage_Sales_Helper_Data')->__('Assign Order BugsCoverage to State');
    }
}

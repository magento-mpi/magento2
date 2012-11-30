<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml customers by orders report content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Report_Customer_Orders extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_controller = 'report_customer_orders';
        $this->_headerText = Mage::helper('Mage_Reports_Helper_Data')->__('Customers by number of orders');
        parent::_construct();
        $this->_removeButton('add');
    }
}

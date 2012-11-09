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

class Mage_Reports_Block_Adminhtml_Customer_Orders extends Mage_Backend_Block_Widget_Grid_Container
{
    protected $_blockGroup = 'Mage_Reports';

    protected function _construct()
    {
        $this->_controller = 'adminhtml_report_customer_orders';
        $this->_headerText = Mage::helper('Mage_Reports_Helper_Data')->__('Customers by number of orders');
        parent::_construct();
        $this->_removeButton('add');
    }
}

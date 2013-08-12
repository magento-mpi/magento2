<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend customers by totals report content block
 *
 * @category   Mage
 * @package    Magento_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Reports_Block_Adminhtml_Customer_Totals extends Mage_Backend_Block_Widget_Grid_Container
{
    protected $_blockGroup = 'Magento_Reports';

    protected function _construct()
    {
        $this->_controller = 'report_customer_totals';
        $this->_headerText = Mage::helper('Magento_Reports_Helper_Data')->__('Customers by Orders Total');
        parent::_construct();
        $this->_removeButton('add');
    }
}

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
 * Backend customers by orders report content block
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Reports_Block_Adminhtml_Customer_Orders extends Magento_Backend_Block_Widget_Grid_Container
{
    /**
     * Define children block group
     *
     * @var string
     */
    protected $_blockGroup = 'Magento_Reports';

    protected function _construct()
    {
        $this->_controller = 'report_customer_orders';
        $this->_headerText = __('Customers by number of orders');
        parent::_construct();
        $this->_removeButton('add');
    }
}

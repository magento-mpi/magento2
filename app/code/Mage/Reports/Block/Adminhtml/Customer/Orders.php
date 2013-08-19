<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend customers by orders report content block
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Reports_Block_Adminhtml_Customer_Orders extends Mage_Backend_Block_Widget_Grid_Container
{
    /**
     * Define children block group
     *
     * @var string
     */
    protected $_blockGroup = 'Mage_Reports';

    protected function _construct()
    {
        $this->_controller = 'report_customer_orders';
        $this->_headerText = __('Customers by number of orders');
        parent::_construct();
        $this->_removeButton('add');
    }
}

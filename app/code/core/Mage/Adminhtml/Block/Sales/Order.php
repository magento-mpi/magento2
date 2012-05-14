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
 * Adminhtml sales orders block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'sales_order';
        $this->_headerText = Mage::helper('Mage_Sales_Helper_Data')->__('Orders');
        $this->_addButtonLabel = Mage::helper('Mage_Sales_Helper_Data')->__('Create New Order');
        parent::__construct();
        if (!Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('sales/order/actions/create')) {
            $this->_removeButton('add');
        }
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/sales_order_create/start');
    }

}

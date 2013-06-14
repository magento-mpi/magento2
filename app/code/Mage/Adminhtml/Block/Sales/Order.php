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
    protected function _construct()
    {
        $this->_controller = 'sales_order';
        $this->_headerText = Mage::helper('Mage_Sales_Helper_Data')->__('Orders');
        $this->_addButtonLabel = Mage::helper('Mage_Sales_Helper_Data')->__('Create New Order');
        parent::_construct();
        if (!$this->_authorization->isAllowed('Mage_Sales::create')) {
            $this->_removeButton('add');
        }
    }

    /**
     * Retrieve url for order creation
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/sales_order_create/start');
    }
}

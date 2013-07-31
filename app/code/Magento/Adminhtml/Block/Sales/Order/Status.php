<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order's status management block
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_Status extends Mage_Backend_Block_Widget_Grid_Container
{
    /**
     * Class constructor
     */
    protected function _construct()
    {
        $this->_controller = 'sales_order_status';
        $this->_headerText = Mage::helper('Mage_Sales_Helper_Data')->__('Order Statuses');
        $this->_addButtonLabel = Mage::helper('Mage_Sales_Helper_Data')->__('Create New Status');
        $this->_addButton('assign', array(
            'label'     => Mage::helper('Mage_Sales_Helper_Data')->__('Assign Status to State'),
            'onclick'   => 'setLocation(\'' . $this->getAssignUrl() .'\')',
            'class'     => 'add',
        ));
        parent::_construct();
    }

    /**
     * Create url getter
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/sales_order_status/new');
    }

    /**
     * Assign url getter
     *
     * @return string
     */
    public function getAssignUrl()
    {
        return $this->getUrl('*/sales_order_status/assign');
    }
}

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
 * Order view tabs
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_View_Tabs extends Magento_Adminhtml_Block_Widget_Tabs
{
    /**
     * Retrieve available order
     *
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($this->hasOrder()) {
            return $this->getData('order');
        }
        if (Mage::registry('current_order')) {
            return Mage::registry('current_order');
        }
        if (Mage::registry('order')) {
            return Mage::registry('order');
        }
        Mage::throwException(__('We cannot get the order instance.'));
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_view_tabs');
        $this->setDestElementId('sales_order_view');
        $this->setTitle(__('Order View'));
    }

}

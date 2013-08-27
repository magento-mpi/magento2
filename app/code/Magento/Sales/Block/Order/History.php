<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales order history block
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Sales_Block_Order_History extends Magento_Core_Block_Template
{

    protected $_template = 'order/history.phtml';


    protected function _construct()
    {
        parent::_construct();


        $orders = Mage::getResourceModel('Magento_Sales_Model_Resource_Order_Collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer()->getId())
            ->addFieldToFilter('state', array('in' => Mage::getSingleton('Magento_Sales_Model_Order_Config')->getVisibleOnFrontStates()))
            ->setOrder('created_at', 'desc')
        ;

        $this->setOrders($orders);

        if (Mage::app()->getFrontController()->getAction()) {
            Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(
                __('My Orders')
            );
        }
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('Magento_Page_Block_Html_Pager', 'sales.order.history.pager')
            ->setCollection($this->getOrders());
        $this->setChild('pager', $pager);
        $this->getOrders()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getViewUrl($order)
    {
        return $this->getUrl('*/*/view', array('order_id' => $order->getId()));
    }

    public function getTrackUrl($order)
    {
        return $this->getUrl('*/*/track', array('order_id' => $order->getId()));
    }

    public function getReorderUrl($order)
    {
        return $this->getUrl('*/*/reorder', array('order_id' => $order->getId()));
    }

    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}

<?php
/**
 * Customer orders controller
 *
 * @package    Mage
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_OrderController extends Mage_Core_Controller_Front_Action
{
    /**
     * Default account page
     *
     */
    public function historyAction() 
    {
        $this->loadLayout();
        
        $orders = Mage::getModel('sales_resource', 'order_collection');
        $orders->addAttributeSelect('self');
        $orders->addAttributeFilter('self/customer_id', Mage::getSingleton('customer', 'session')->getCustomerId());
        $orders->setOrder('self/created_at');
        $orders->loadData();
        
        $block = $this->getLayout()->createBlock('tpl', 'customer.orders')
            ->setTemplate('customer/orders.phtml')
            ->assign('orders', $orders);
        $this->getLayout()->getBlock('content')->append($block);

        $this->renderLayout();        
    }
    
    public function viewAction()
    {
        $this->loadLayout();
        $orderId = $this->getRequest()->getParam('order', false);
        if (!$orderId) {
            $this->_forward('noRoute');
            return;
        }
        
        $order = Mage::getModel('sales', 'order')->load($orderId);
        
        $block = $this->getLayout()->createBlock('tpl', 'customer.orders')
            ->setTemplate('customer/order/view.phtml')
            ->assign('order', $order);

        $payment = $order->getPayment();
        
        if ($payment) {
            $paymentMethodConfig = Mage::getConfig()->getNode('global/salesPaymentMethods/'.$payment->getMethod());
            if (!empty($paymentMethodConfig)) {
                $className = $paymentMethodConfig->getClassName();
                $paymentMethod = new $className();
                $paymentBlock = $paymentMethod->setPayment($payment)->createInfoBlock($block->getData('name').'.payment');
                $block->setChild('payment', $paymentBlock);
            } else {
                $block->assign('payment', '');
            }
        } else {
            $block->assign('payment', '');
        }
            
        $this->getLayout()->getBlock('content')->append($block);

        $this->renderLayout();        
    }
}// Class Mage_Customer_AccountController END

<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Enter description here...
     *
     * @return Mage_Adminhtml_Sales_OrderController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/order')
            ->_addBreadcrumb(__('Sales'), __('Sales'))
            ->_addBreadcrumb(__('Orders'), __('Orders'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/sales_order'))
            ->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('order_id');
        $model = Mage::getModel('sales/order');

        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(__('This order no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(__('This order no longer exists'));
            $this->_redirect('*/*/');
            return;
        }

        Mage::register('sales_order', $model);

        $this->_initAction()
            ->_addBreadcrumb(__('Edit Order'), __('Edit Order'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/sales_order_edit'))
            ->renderLayout();
    }

    public function viewAction()
    {
        $id = $this->getRequest()->getParam('order_id');
        $model = Mage::getModel('sales/order');

        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(__('This order no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(__('This order no longer exists'));
            $this->_redirect('*/*/');
            return;
        }

        Mage::register('sales_order', $model);

        $this->_initAction()
            ->_addBreadcrumb(__('View Order'), __('View Order'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/sales_order_view'))
            ->renderLayout();
    }

    public function deleteAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order');

        if ($orderId) {
            $order->load($orderId);
            if (! $order->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(__('This order no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(__('This order no longer exists'));
            $this->_redirect('*/*/');
            return;
        }

        $order->addStatus(4); // Canceled

        try {
            $order->save();
            Mage::getSingleton('adminhtml/session')->addSuccess(__('Order was cancelled successfully'));
            $this->_redirect('*/sales_order/view', array('order_id' => $orderId));
            return;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(__('Order was not cancelled'));
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/sales_order/view', array('order_id' => $orderId));
            return;
        }

    }

    public function saveAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order');
        /* @var $order Mage_Sales_Model_Order */

        if ($orderId) {
            $order->load($orderId);
            if (! $order->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(__('This order no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(__('This order no longer exists'));
            $this->_redirect('*/*/');
            return;
        }

        if ($newStatus = $this->getRequest()->getParam('new_status')) {

            $notifyCustomer = $this->getRequest()->getParam('notify_customer', false);

            $order->addStatus($newStatus, $this->getRequest()->getParam('comments', ''), $notifyCustomer);

            if ($notifyCustomer) {
                $order->sendOrderUpdateEmail();
            }

            try {
                $order->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(__('Order status was changed successfully'));
                $this->_redirect('*/sales_order/view', array('order_id' => $orderId));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(__('Order was not changed'));
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/sales_order/view', array('order_id' => $orderId));
                return;
            }
        }

        $this->_redirect('*/sales_order/view', array('order_id' => $orderId));
    }

    protected function _isAllowed()
    {
	    return Mage::getSingleton('admin/session')->isAllowed('sales/order');
    }
}

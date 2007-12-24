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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales orders controller
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Sales_OrderController extends Mage_Core_Controller_Front_Action
{

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::helper('customer')->getLoginUrl();
        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    /**
     * Customer order history
     */
    public function historyAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _canViewOrder($order)
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        if ($order->getId() && $order->getCustomerId() && $order->getCustomerId() == $customerId) {
            return true;
        }
        return false;
    }

    /**
     * Order view page
     */
    public function viewAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderId);
        if ($this->_canViewOrder($order)) {
            Mage::register('current_order', $order);
            $this->loadLayout();
            $this->renderLayout();
        }
        else {
            $this->_redirect('*/*/history');
        }
    }

    public function detailsAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function printAction()
    {
        $this->loadLayout('print');
        $this->renderLayout();
    }

    public function testAction()
    {
        $order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id'));
        $order->sendNewOrderEmail();
    }
}
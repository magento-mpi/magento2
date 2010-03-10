<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_SalesPool
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Sales order pool controller for viewing pool orders grid
 *
 */
class Enterprise_SalesPool_Adminhtml_Sales_Order_PoolController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Pool grid action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Pool grid ajax action
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Flush orders from pool
     *
     */
    public function flushAllAction()
    {
        try {
            Mage::getModel('enterprise_salespool/pool')->flushAllOrders();
            $this->_getSession()->addSuccess(Mage::helper('enterprise_salespool')->__('All orders successfully processed'));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError(Mage::helper('enterprise_salespool')->__('Unable to process all orders'));
        }
        $this->_redirect('*/*/');
    }

    /**
     * Flush orders from pool
     *
     */
    public function massFlushAction()
    {
        $orderIds = $this->getRequest()->getParam('order_ids');


        if (!empty($orderIds)) {
            try {
                $flushedOrdersCount = Mage::getModel('enterprise_salespool/pool')->flushOrderById($orderIds);
                if ($flushedOrdersCount > 0) {
                    $this->_getSession()->addSuccess(Mage::helper('enterprise_salespool')->__('%d order(s) successfully processed', $flushedOrdersCount));
                } else {
                   $this->_getSession()->addError(Mage::helper('enterprise_salespool')->__('There are no orders for process'));
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('enterprise_salespool')->__('Unable to process selected orders'));
            }
        } else {
            $this->_getSession()->addError(Mage::helper('enterprise_salespool')->__('Please select orders'));
        }

        $this->_redirect('*/*/');
    }

    /**
     * Flush orders from pool
     *
     */
    public function flushAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');


        if (!empty($orderId)) {
            try {
                $flushedOrdersCount = Mage::getModel('enterprise_salespool/pool')->flushOrderById($orderId);
                if ($flushedOrdersCount > 0) {
                    $this->_getSession()->addSuccess(Mage::helper('enterprise_salespool')->__('Order successfully processed'));
                } else {
                   $this->_getSession()->addError(Mage::helper('enterprise_salespool')->__('This order has been already processed'));
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('enterprise_salespool')->__('Unable to process selected orders'));
            }
        } else {
            $this->_getSession()->addError(Mage::helper('enterprise_salespool')->__('There is no order to process'));
            $this->_redirect('*/*/');
        }

        $this->_redirect('*/sales_order/view', array('order_id'=>$orderId));
    }

    /**
     * Check potential problems with pool functionality enable
     */
    public function checkAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Check Acl rules
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch (strtolower($this->getRequest()->getActionName())) {
            case 'flush':
            case 'flushAll':
                $acl = 'sales/order/pool/flush';
                break;

            default:
                $acl = 'sales/order/pool';
                break;
        }

        return Mage::getSingleton('admin/session')->isAllowed($acl);
    }
}

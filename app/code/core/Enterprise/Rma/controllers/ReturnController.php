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
 * @package     Enterprise_Rma
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Rma_ReturnController extends Mage_Core_Controller_Front_Action
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
        $loginUrl = Mage::helper('Mage_Customer_Helper_Data')->getLoginUrl();

        if (!Mage::getSingleton('Mage_Customer_Model_Session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    /**
     * Customer returns history
     */
    public function historyAction()
    {
        if (!$this->_isEnabledOnFront()) {
            $this->_forward('noRoute');
            return false;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('Enterprise_Rma_Helper_Data')->__('My Returns'));

        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }

    /**
     * Customer create new return
     */
    public function createAction()
    {
        $orderId    = (int)$this->getRequest()->getParam('order_id');
        $order      = Mage::getModel('Mage_Sales_Model_Order')->load($orderId);
        if (empty($orderId)) {
            $this->_redirect('sales/order/history');
            return;
        }
        Mage::register('current_order', $order);

        if (!$this->_loadOrderItems($orderId)) {
            return;
        }

        if ($this->_canViewOrder($order)) {
            $post = $this->getRequest()->getPost();
            if (($post) && !empty($post['items'])) {
                try {
                    $rmaModel = Mage::getModel('Enterprise_Rma_Model_Rma');
                    $rmaData = array(
                        'status'                => Enterprise_Rma_Model_Rma_Source_Status::STATE_PENDING,
                        'date_requested'        => Mage::getSingleton('Mage_Core_Model_Date')->gmtDate(),
                        'order_id'              => $order->getId(),
                        'order_increment_id'    => $order->getIncrementId(),
                        'store_id'              => $order->getStoreId(),
                        'customer_id'           => $order->getCustomerId(),
                        'order_date'            => $order->getCreatedAt(),
                        'customer_name'         => $order->getCustomerName(),
                        'customer_custom_email' => $post['customer_custom_email']
                    );
                    $result = $rmaModel->setData($rmaData)->saveRma();
                    if (!$result) {
                        $this->_redirectError(Mage::getUrl('*/*/create', array('order_id'  => $orderId)));
                        return;
                    }
                    $result->sendNewRmaEmail();
                    if (isset($post['rma_comment']) && !empty($post['rma_comment'])) {
                        Mage::getModel('Enterprise_Rma_Model_Rma_Status_History')
                            ->setRmaEntityId($rmaModel->getId())
                            ->setComment($post['rma_comment'])
                            ->setIsVisibleOnFront(true)
                            ->setStatus($rmaModel->getStatus())
                            ->setCreatedAt(Mage::getSingleton('Mage_Core_Model_Date')->gmtDate())
                            ->save();
                    }
                    Mage::getSingleton('Mage_Core_Model_Session')->addSuccess(
                        Mage::helper('Enterprise_Rma_Helper_Data')->__('Return #%s has been submitted successfully', $rmaModel->getIncrementId())
                    );
                    $this->_redirectSuccess(Mage::getUrl('*/*/history'));
                    return;
                } catch (Exception $e) {
                    Mage::getSingleton('Mage_Core_Model_Session')->addError(
                        Mage::helper('Enterprise_Rma_Helper_Data')->__('Cannot create New Return, try again later')
                    );
                    Mage::logException($e);
                }
            }
            $this->loadLayout();
            $this->_initLayoutMessages('core/session');
            $this->getLayout()->getBlock('head')->setTitle(Mage::helper('Enterprise_Rma_Helper_Data')->__('Create New Return'));
            if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
                $block->setRefererUrl($this->_getRefererUrl());
            }
            $this->renderLayout();
        } else {
            $this->_redirect('sales/order/history');
        }
    }

    /**
     * Check order view availability
     *
     * @param   Enterprise_Rma_Model_Rma | Mage_Sales_Model_Order $item
     * @return  bool
     */
    protected function _canViewOrder($item)
    {
        $customerId = Mage::getSingleton('Mage_Customer_Model_Session')->getCustomerId();
        if ($item->getId() && $item->getCustomerId() && ($item->getCustomerId() == $customerId)) {
            return true;
        }
        return false;
    }

    /**
     * Try to load valid rma by entity_id and register it
     *
     * @param int $entityId
     * @return bool
     */
    protected function _loadValidRma($entityId = null)
    {
        if (null === $entityId) {
            $entityId = (int) $this->getRequest()->getParam('entity_id');
        }
        if (!$entityId || !$this->_isEnabledOnFront()) {
            $this->_forward('noRoute');
            return false;
        }

        $rma = Mage::getModel('Enterprise_Rma_Model_Rma')->load($entityId);

        if ($this->_canViewOrder($rma)) {
            Mage::register('current_rma', $rma);
            return true;
        } else {
            $this->_redirect('*/*/history');
        }
        return false;
    }

    /**
     * Try to load valid collection of ordered items
     *
     * @param int $entityId
     * @return bool
     */
    protected function _loadOrderItems($orderId)
    {
        if (Mage::helper('Enterprise_Rma_Helper_Data')->canCreateRma($orderId)) {
            return true;
        }

        $incrementId    = Mage::registry('current_order')->getIncrementId();
        $message        = Mage::helper('Enterprise_Rma_Helper_Data')->__('Cannot create rma for order #%s.', $incrementId);
        Mage::getSingleton('Mage_Core_Model_Session')->addError($message);
        $this->_redirect('sales/order/history');
        return false;
    }

    /**
     * RMA view page
     */
    public function viewAction()
    {
        if (!$this->_loadValidRma()) {
            $this->_redirect('*/*/history');
            return;
        }

        $order = Mage::getModel('Mage_Sales_Model_Order')->load(
            Mage::registry('current_rma')->getOrderId()
        );
        Mage::register('current_order', $order);

        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->getLayout()
            ->getBlock('head')
            ->setTitle(Mage::helper('Enterprise_Rma_Helper_Data')->__('RMA #%s', Mage::registry('current_rma')->getIncrementId()));

        $this->renderLayout();
    }

    /**
     * View RMA for Order
     */
    public function returnsAction()
    {
        $orderId    = (int) $this->getRequest()->getParam('order_id');
        $customerId = Mage::getSingleton('Mage_Customer_Model_Session')->getCustomerId();

        if (!$orderId || !$this->_isEnabledOnFront()) {
            $this->_forward('noRoute');
            return false;
        }

        $order = Mage::getModel('Mage_Sales_Model_Order')->load($orderId);

        $availableStates = Mage::getSingleton('Mage_Sales_Model_Order_Config')->getVisibleOnFrontStates();
        if ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerId)
            && in_array($order->getState(), $availableStates, $strict = true)
            ) {
            Mage::register('current_order', $order);
        } else {
            $this->_redirect('*/*/history');
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');

        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('sales/order/history');
        }
        $this->renderLayout();
    }

    /**
     * Add RMA comment action
     */
    public function addCommentAction()
    {
        if ($this->_loadValidRma()) {
            try {
                $response   = false;
                $comment    = $this->getRequest()->getPost('comment');
                $comment    = trim(strip_tags($comment));

                if (!empty($comment)) {
                    $result = Mage::getModel('Enterprise_Rma_Model_Rma_Status_History')
                        ->setRmaEntityId(Mage::registry('current_rma')->getEntityId())
                        ->setComment($comment)
                        ->setIsVisibleOnFront(true)
                        ->setStatus(Mage::registry('current_rma')->getStatus())
                        ->setCreatedAt(Mage::getSingleton('Mage_Core_Model_Date')->gmtDate())
                        ->save();
                    $result->setStoreId(Mage::registry('current_rma')->getStoreId());
                    $result->sendCustomerCommentEmail();
                } else {
                    Mage::throwException(Mage::helper('Enterprise_Rma_Helper_Data')->__('Enter valid message.'));
                }
            } catch (Mage_Core_Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            } catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => Mage::helper('Enterprise_Rma_Helper_Data')->__('Cannot add message.')
                );
            }
            if (is_array($response)) {
               Mage::getSingleton('Mage_Core_Model_Session')->addError($response['message']);
            }
            $this->_redirect('*/*/view', array('entity_id' => (int)$this->getRequest()->getParam('entity_id')));
            return;
        }
        return;
    }
    /**
     * Add Tracking Number action
     */
    public function addLabelAction()
    {
        if ($this->_loadValidRma()) {
            try {
                $rma = Mage::registry('current_rma');

                if (!$rma->isAvailableForPrintLabel()) {
                    Mage::throwException(Mage::helper('Enterprise_Rma_Helper_Data')->__('Shipping Labels are not allowed.'));
                }

                $response   = false;
                $number    = $this->getRequest()->getPost('number');
                $number    = trim(strip_tags($number));
                $carrier   = $this->getRequest()->getPost('carrier');
                $carriers  = Mage::helper('Enterprise_Rma_Helper_Data')->getShippingCarriers($rma->getStoreId());

                if (!isset($carriers[$carrier])) {
                    Mage::throwException(Mage::helper('Enterprise_Rma_Helper_Data')->__('Select valid carrier.'));
                }

                if (empty($number)) {
                    Mage::throwException(Mage::helper('Enterprise_Rma_Helper_Data')->__('Enter valid Tracking Number.'));
                }

                Mage::getModel('Enterprise_Rma_Model_Shipping')
                    ->setRmaEntityId($rma->getEntityId())
                    ->setTrackNumber($number)
                    ->setCarrierCode($carrier)
                    ->setCarrierTitle($carriers[$carrier])
                    ->save();

            } catch (Mage_Core_Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            } catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => Mage::helper('Enterprise_Rma_Helper_Data')->__('Cannot add label.')
                );
            }
        } else {
            $response = array(
                'error'     => true,
                'message'   => Mage::helper('Enterprise_Rma_Helper_Data')->__('Wrong RMA Selected.')
            );
        }
        if (is_array($response)) {
            Mage::getSingleton('Mage_Core_Model_Session')->setErrorMessage($response['message']);
        }

        $this->loadLayout();
        $response = $this->getLayout()->getBlock('enterprise_rma_return_tracking')->toHtml();
        $this->getResponse()->setBody($response);

        return;
    }
    /**
     * Delete Tracking Number action
     */
    public function delLabelAction()
    {
        if ($this->_loadValidRma()) {
            try {
                $rma = Mage::registry('current_rma');

                if (!$rma->isAvailableForPrintLabel()) {
                    Mage::throwException(Mage::helper('Enterprise_Rma_Helper_Data')->__('Shipping Labels are not allowed.'));
                }

                $response   = false;
                $number    = intval($this->getRequest()->getPost('number'));

                if (empty($number)) {
                    Mage::throwException(Mage::helper('Enterprise_Rma_Helper_Data')->__('Enter valid Tracking Number.'));
                }

                $trackingNumber = Mage::getModel('Enterprise_Rma_Model_Shipping')
                    ->load($number);
                if ($trackingNumber->getRmaEntityId() !== $rma->getId()) {
                    Mage::throwException(Mage::helper('Enterprise_Rma_Helper_Data')->__('Wrong RMA Selected.'));
                }
                $trackingNumber->delete();

            } catch (Mage_Core_Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            } catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => Mage::helper('Enterprise_Rma_Helper_Data')->__('Cannot delete label.')
                );
            }
        } else {
            $response = array(
                'error'     => true,
                'message'   => Mage::helper('Enterprise_Rma_Helper_Data')->__('Wrong RMA Selected.')
            );
        }
        if (is_array($response)) {
            Mage::getSingleton('Mage_Core_Model_Session')->setErrorMessage($response['message']);
        }

        $this->loadLayout();
        $response = $this->getLayout()->getBlock('enterprise_rma_return_tracking')->toHtml();
        $this->getResponse()->setBody($response);

        return;
    }

    /**
     * Checks whether RMA module is enabled in system config
     *
     * @return boolean
     */
    protected function _isEnabledOnFront()
    {
        return Mage::helper('Enterprise_Rma_Helper_Data')->isEnabled();
    }
}

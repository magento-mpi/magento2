<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Rma_Controller_Guest extends Magento_Core_Controller_Front_Action
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Magento_Core_Model_Session
     */
    protected $_session;

    /**
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Session $session
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Session $session
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_session = $session;
        parent::__construct($context);
    }

    /**
     * View all returns
     */
    public function returnsAction()
    {
        if (!$this->_objectManager->get('Magento_Rma_Helper_Data')->isEnabled()
            || !$this->_objectManager->get('Magento_Sales_Helper_Guest')->loadValidOrder()) {
            $this->_forward('noRoute');
            return;
        }
        $this->loadLayout();
        $this->_objectManager->get('Magento_Sales_Helper_Guest')->getBreadcrumbs($this);
        $this->renderLayout();
    }

    /**
     * Check order view availability
     *
     * @param   Magento_Rma_Model_Rma $rma
     * @return  bool
     */
    protected function _canViewRma($rma)
    {
        $currentOrder = $this->_coreRegistry->registry('current_order');
        if ($rma->getOrderId() && ($rma->getOrderId() === $currentOrder->getId())) {
            return true;
        }
        return false;
    }

    /**
     * View concrete rma
     */
    public function viewAction()
    {
        if (!$this->_loadValidRma()) {
            $this->_redirect('*/*/returns');
            return;
        }

        $this->loadLayout();
        $this->_objectManager->get('Magento_Sales_Helper_Guest')->getBreadcrumbs($this);
        $this->getLayout()
            ->getBlock('head')
            ->setTitle(__('Return #%1', $this->_coreRegistry->registry('current_rma')->getIncrementId()));
        $this->renderLayout();
    }

    /**
     * Try to load valid rma by entity_id and register it
     *
     * @param int $entityId
     * @return bool
     */
    protected function _loadValidRma($entityId = null)
    {
        if (!$this->_objectManager->get('Magento_Rma_Helper_Data')->isEnabled() ||
            !$this->_objectManager->get('Magento_Sales_Helper_Guest')->loadValidOrder()) {
            return;
        }

        if (null === $entityId) {
            $entityId = (int) $this->getRequest()->getParam('entity_id');
        }

        if (!$entityId) {
            $this->_forward('noRoute');
            return false;
        }
        /** @var $rma Magento_Rma_Model_Rma */
        $rma = $this->_objectManager->create('Magento_Rma_Model_Rma')->load($entityId);

        if ($this->_canViewRma($rma)) {
            $this->_coreRegistry->register('current_rma', $rma);
            return true;
        } else {
            $this->_redirect('*/*/returns');
        }
        return false;
    }

    /**
     * Customer create new return
     */
    public function createAction()
    {
        if (!$this->_objectManager->get('Magento_Sales_Helper_Guest')->loadValidOrder()) {
            return;
        }
        $order      = $this->_coreRegistry->registry('current_order');
        $orderId    = $order->getId();
        if (!$this->_loadOrderItems($orderId)) {
            return;
        }

        $post = $this->getRequest()->getPost();
        if (($post) && !empty($post['items'])) {
            try {
                /** @var $urlModel Magento_Core_Model_Url */
                $urlModel = $this->_objectManager->get('Magento_Core_Model_Url');
                /** @var $rmaModel Magento_Rma_Model_Rma */
                $rmaModel = $this->_objectManager->create('Magento_Rma_Model_Rma');
                $rmaData = array(
                    'status'                => Magento_Rma_Model_Rma_Source_Status::STATE_PENDING,
                    'date_requested'        => $this->_objectManager->get('Magento_Core_Model_Date')->gmtDate(),
                    'order_id'              => $order->getId(),
                    'order_increment_id'    => $order->getIncrementId(),
                    'store_id'              => $order->getStoreId(),
                    'customer_id'           => $order->getCustomerId(),
                    'order_date'            => $order->getCreatedAt(),
                    'customer_name'         => $order->getCustomerName(),
                    'customer_custom_email' => $post['customer_custom_email']
                );
                $result = $rmaModel->setData($rmaData)->saveRma($post);
                if (!$result) {
                    $this->_redirectError($urlModel->getUrl('*/*/create', array('order_id'  => $orderId)));
                    return;
                }
                $result->sendNewRmaEmail();
                if (isset($post['rma_comment']) && !empty($post['rma_comment'])) {
                    /** @var $statusHistory Magento_Rma_Model_Rma_Status_History */
                    $statusHistory = $this->_objectManager->create('Magento_Rma_Model_Rma_Status_History');
                    $statusHistory->setRmaEntityId($rmaModel->getId())
                        ->setComment($post['rma_comment'])
                        ->setIsVisibleOnFront(true)
                        ->setStatus($rmaModel->getStatus())
                        ->setCreatedAt($this->_objectManager->get('Magento_Core_Model_Date')->gmtDate())
                        ->save();
                }
                $this->_session->addSuccess(
                    __('You submitted Return #%1.', $rmaModel->getIncrementId())
                );
                $this->_redirectSuccess($urlModel->getUrl('*/*/returns'));
                return;
            } catch (Exception $e) {
                $this->_session->addError(
                    __('We cannot create a new return transaction. Please try again later.')
                );
                $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
            }
        }
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Core_Model_Session');
        $this->getLayout()->getBlock('head')->setTitle(__('Create New Return'));
        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }

    /**
     * Try to load valid collection of ordered items
     *
     * @param int $orderId
     * @return bool
     */
    protected function _loadOrderItems($orderId)
    {
        if ($this->_objectManager->get('Magento_Rma_Helper_Data')->canCreateRma($orderId)) {
            return true;
        }

        $incrementId = $this->_coreRegistry->registry('current_order')->getIncrementId();
        $message = __('We cannot create a return transaction for order #%1.', $incrementId);
        $this->_session->addError($message);
        $this->_redirect('sales/order/history');
        return false;
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
                    /** @var $statusHistory Magento_Rma_Model_Rma_Status_History */
                    $statusHistory = $this->_objectManager->create('Magento_Rma_Model_Rma_Status_History');
                    $result = $statusHistory
                        ->setRmaEntityId($this->_coreRegistry->registry('current_rma')->getEntityId())
                        ->setComment($comment)
                        ->setIsVisibleOnFront(true)
                        ->setStatus($this->_coreRegistry->registry('current_rma')->getStatus())
                        ->setCreatedAt($this->_objectManager->get('Magento_Core_Model_Date')->gmtDate())
                        ->save();
                    $result->setStoreId($this->_coreRegistry->registry('current_rma')->getStoreId());
                    $result->sendCustomerCommentEmail();
                } else {
                    throw new Magento_Core_Exception(__('Please enter a valid message.'));
                }
            } catch (Magento_Core_Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            } catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => __('We cannot add a message.')
                );
            }
            if (is_array($response)) {
                $this->_session->addError($response['message']);
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
                $rma = $this->_coreRegistry->registry('current_rma');

                if (!$rma->isAvailableForPrintLabel()) {
                    throw new Magento_Core_Exception(__('Shipping Labels are not allowed.'));
                }

                $response   = false;
                $number    = $this->getRequest()->getPost('number');
                $number    = trim(strip_tags($number));
                $carrier   = $this->getRequest()->getPost('carrier');
                $carriers  = $this->_objectManager->get('Magento_Rma_Helper_Data')
                    ->getShippingCarriers($rma->getStoreId());

                if (!isset($carriers[$carrier])) {
                    throw new Magento_Core_Exception(__('Please select a valid carrier.'));
                }

                if (empty($number)) {
                    throw new Magento_Core_Exception(__('Please enter a valid tracking number.'));
                }
                /** @var $rmaShipping Magento_Rma_Model_Shipping */
                $rmaShipping = $this->_objectManager->create('Magento_Rma_Model_Shipping');
                $rmaShipping->setRmaEntityId($rma->getEntityId())
                    ->setTrackNumber($number)
                    ->setCarrierCode($carrier)
                    ->setCarrierTitle($carriers[$carrier])
                    ->save();

            } catch (Magento_Core_Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            } catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => __('We cannot add a label.')
                );
            }
        } else {
            $response = array(
                'error'     => true,
                'message'   => __('The wrong RMA was selected.')
            );
        }
        if (is_array($response)) {
            $this->_session->setErrorMessage($response['message']);
        }

        $this->addPageLayoutHandles();
        $this->loadLayout(false)
            ->renderLayout();
        return;
    }
    /**
     * Delete Tracking Number action
     */
    public function delLabelAction()
    {
        if ($this->_loadValidRma()) {
            try {
                $rma = $this->_coreRegistry->registry('current_rma');

                if (!$rma->isAvailableForPrintLabel()) {
                    throw new Magento_Core_Exception(__('Shipping Labels are not allowed.'));
                }

                $response   = false;
                $number    = intval($this->getRequest()->getPost('number'));

                if (empty($number)) {
                    throw new Magento_Core_Exception(__('Please enter a valid tracking number.'));
                }
                /** @var $trackingNumber Magento_Rma_Model_Shipping */
                $trackingNumber = $this->_objectManager->create('Magento_Rma_Model_Shipping')
                    ->load($number);
                if ($trackingNumber->getRmaEntityId() !== $rma->getId()) {
                    throw new Magento_Core_Exception(__('The wrong RMA was selected.'));
                }
                $trackingNumber->delete();

            } catch (Magento_Core_Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            } catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => __('We cannot delete the label.')
                );
            }
        } else {
            $response = array(
                'error'     => true,
                'message'   => __('The wrong RMA was selected.')
            );
        }
        if (is_array($response)) {
            $this->_session->setErrorMessage($response['message']);
        }

        $this->addPageLayoutHandles();
        $this->loadLayout(false)
            ->renderLayout();
        return;
    }


}

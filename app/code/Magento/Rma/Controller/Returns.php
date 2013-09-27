<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Controller;

class Returns extends \Magento\Core\Controller\Front\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Core\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Core\Model\Session $session
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Core\Model\Session $session,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_session = $session;
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $loginUrl = $this->_objectManager->get('Magento\Customer\Helper\Data')->getLoginUrl();

        if (!$this->_customerSession->authenticate($this, $loginUrl)) {
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
        $this->_initLayoutMessages('Magento\Catalog\Model\Session');

        $this->getLayout()->getBlock('head')->setTitle(__('My Returns'));

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
        $orderId = (int)$this->getRequest()->getParam('order_id');
        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
        if (empty($orderId)) {
            $this->_redirect('sales/order/history');
            return;
        }
        $this->_coreRegistry->register('current_order', $order);

        if (!$this->_loadOrderItems($orderId)) {
            return;
        }

        if ($this->_canViewOrder($order)) {
            $post = $this->getRequest()->getPost();
            if (($post) && !empty($post['items'])) {
                try {
                    /** @var $urlModel \Magento\Core\Model\Url */
                    $urlModel = $this->_objectManager->get('Magento\Core\Model\Url');
                    /** @var $dateModel \Magento\Core\Model\Date */
                    $dateModel = $this->_objectManager->get('Magento\Core\Model\Date');
                    /** @var $rmaModel \Magento\Rma\Model\Rma */
                    $rmaModel = $this->_objectManager->create('Magento\Rma\Model\Rma');
                    $rmaData = array(
                        'status'                => \Magento\Rma\Model\Rma\Source\Status::STATE_PENDING,
                        'date_requested'        => $dateModel->gmtDate(),
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
                        /** @var $statusHistory \Magento\Rma\Model\Rma\Status\History */
                        $statusHistory = $this->_objectManager->create('Magento\Rma\Model\Rma\Status\History');
                        $statusHistory->setRmaEntityId($rmaModel->getId())
                            ->setComment($post['rma_comment'])
                            ->setIsVisibleOnFront(true)
                            ->setStatus($rmaModel->getStatus())
                            ->setCreatedAt($dateModel->gmtDate())
                            ->save();
                    }
                    $this->_session->addSuccess(
                        __('You submitted Return #%1.', $rmaModel->getIncrementId())
                    );
                    $this->_redirectSuccess($urlModel->getUrl('*/*/history'));
                    return;
                } catch (\Exception $e) {
                    $this->_session->addError(
                        __('We cannot create a new return transaction. Please try again later.')
                    );
                    $this->_objectManager->get('Magento\Core\Model\Logger')->logException($e);
                }
            }
            $this->loadLayout();
            $this->_initLayoutMessages('Magento\Core\Model\Session');
            $this->getLayout()->getBlock('head')->setTitle(__('Create New Return'));
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
     * @param \Magento\Rma\Model\Rma|\Magento\Sales\Model\Order $item
     * @return bool
     */
    protected function _canViewOrder($item)
    {
        $customerId = $this->_customerSession->getCustomerId();
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

        /** @var $rma \Magento\Rma\Model\Rma */
        $rma = $this->_objectManager->create('Magento\Rma\Model\Rma')->load($entityId);
        if ($this->_canViewOrder($rma)) {
            $this->_coreRegistry->register('current_rma', $rma);
            return true;
        } else {
            $this->_redirect('*/*/history');
        }
        return false;
    }

    /**
     * Try to load valid collection of ordered items
     *
     * @param int $orderId
     * @return bool
     */
    protected function _loadOrderItems($orderId)
    {
        /** @var $rmaHelper \Magento\Rma\Helper\Data */
        $rmaHelper = $this->_objectManager->get('Magento\Rma\Helper\Data');
        if ($rmaHelper->canCreateRma($orderId)) {
            return true;
        }

        $incrementId = $this->_coreRegistry->registry('current_order')->getIncrementId();
        $message = __('We cannot create a return transaction for order #%1.', $incrementId);
        $this->_session->addError($message);
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
        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load(
            $this->_coreRegistry->registry('current_rma')->getOrderId()
        );
        $this->_coreRegistry->register('current_order', $order);

        $this->loadLayout();
        $this->_initLayoutMessages('Magento\Catalog\Model\Session');
        $this->getLayout()
            ->getBlock('head')
            ->setTitle(__('Return #%1', $this->_coreRegistry->registry('current_rma')->getIncrementId()));

        $this->renderLayout();
    }

    /**
     * View RMA for Order
     */
    public function returnsAction()
    {
        $orderId = (int) $this->getRequest()->getParam('order_id');
        $customerId = $this->_customerSession->getCustomerId();

        if (!$orderId || !$this->_isEnabledOnFront()) {
            $this->_forward('noRoute');
            return false;
        }

        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
        /** @var $orderConfig  \Magento\Sales\Model\Order\Config*/
        $orderConfig = $this->_objectManager->get('Magento\Sales\Model\Order\Config');
        $availableStates = $orderConfig->getVisibleOnFrontStates();
        if ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerId)
            && in_array($order->getState(), $availableStates, $strict = true)
            ) {
            $this->_coreRegistry->register('current_order', $order);
        } else {
            $this->_redirect('*/*/history');
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('Magento\Catalog\Model\Session');

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
                    /** @var $dateModel \Magento\Core\Model\Date */
                    $dateModel = $this->_objectManager->get('Magento\Core\Model\Date');
                    /** @var $statusHistory \Magento\Rma\Model\Rma\Status\History */
                    $statusHistory = $this->_objectManager->create('Magento\Rma\Model\Rma\Status\History');
                    $result = $statusHistory
                        ->setRmaEntityId($this->_coreRegistry->registry('current_rma')->getEntityId())
                        ->setComment($comment)
                        ->setIsVisibleOnFront(true)
                        ->setStatus($this->_coreRegistry->registry('current_rma')->getStatus())
                        ->setCreatedAt($dateModel->gmtDate())
                        ->save();
                    $result->setStoreId($this->_coreRegistry->registry('current_rma')->getStoreId());
                    $result->sendCustomerCommentEmail();
                } else {
                    throw new \Magento\Core\Exception(__('Please enter a valid message.'));
                }
            } catch (\Magento\Core\Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            } catch (\Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => __('Cannot add message.')
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
                    throw new \Magento\Core\Exception(__('Shipping Labels are not allowed.'));
                }

                $response   = false;
                $number    = $this->getRequest()->getPost('number');
                $number    = trim(strip_tags($number));
                $carrier   = $this->getRequest()->getPost('carrier');
                $carriers  = $this->_objectManager->get('Magento\Rma\Helper\Data')->getShippingCarriers($rma->getStoreId());

                if (!isset($carriers[$carrier])) {
                    throw new \Magento\Core\Exception(__('Please select a valid carrier.'));
                }

                if (empty($number)) {
                    throw new \Magento\Core\Exception(__('Please enter a valid tracking number.'));
                }

                /** @var $rmaShipping \Magento\Rma\Model\Shipping */
                $rmaShipping = $this->_objectManager->create('Magento\Rma\Model\Shipping');
                $rmaShipping->setRmaEntityId($rma->getEntityId())
                    ->setTrackNumber($number)
                    ->setCarrierCode($carrier)
                    ->setCarrierTitle($carriers[$carrier])
                    ->save();

            } catch (\Magento\Core\Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            } catch (\Exception $e) {
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
                    throw new \Magento\Core\Exception(__('Shipping Labels are not allowed.'));
                }

                $response   = false;
                $number    = intval($this->getRequest()->getPost('number'));

                if (empty($number)) {
                    throw new \Magento\Core\Exception(__('Please enter a valid tracking number.'));
                }

                /** @var $trackingNumber \Magento\Rma\Model\Shipping */
                $trackingNumber = $this->_objectManager->create('Magento\Rma\Model\Shipping')->load($number);
                if ($trackingNumber->getRmaEntityId() !== $rma->getId()) {
                    throw new \Magento\Core\Exception(__('The wrong RMA was selected.'));
                }
                $trackingNumber->delete();

            } catch (\Magento\Core\Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            } catch (\Exception $e) {
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

    /**
     * Checks whether RMA module is enabled in system config
     *
     * @return boolean
     */
    protected function _isEnabledOnFront()
    {
        /** @var $rmaHelper \Magento\Rma\Helper\Data */
        $rmaHelper = $this->_objectManager->get('Magento\Rma\Helper\Data');
        return $rmaHelper->isEnabled();
    }
}

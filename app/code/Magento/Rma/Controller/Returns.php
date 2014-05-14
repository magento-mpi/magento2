<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller;

use Magento\Framework\App\RequestInterface;

class Returns extends \Magento\Framework\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->_objectManager->get('Magento\Customer\Helper\Data')->getLoginUrl();

        if (!$this->_objectManager->get('Magento\Customer\Model\Session')->authenticate($this, $loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * Customer returns history
     *
     * @return false|null
     */
    public function historyAction()
    {
        if (!$this->_isEnabledOnFront()) {
            $this->_forward('noroute');
            return false;
        }

        $this->_view->loadLayout();
        $layout = $this->_view->getLayout();
        $layout->initMessages();
        $layout->getBlock('head')->setTitle(__('My Returns'));

        if ($block = $this->_view->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->renderLayout();
    }

    /**
     * Customer create new return
     *
     * @return void
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

        /** @var \Magento\Framework\Stdlib\DateTime\DateTime $coreDate */
        $coreDate = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime');
        if ($this->_canViewOrder($order)) {
            $post = $this->getRequest()->getPost();
            if ($post && !empty($post['items'])) {
                try {
                    /** @var $urlModel \Magento\Framework\UrlInterface */
                    $urlModel = $this->_objectManager->get('Magento\Framework\UrlInterface');
                    /** @var $rmaModel \Magento\Rma\Model\Rma */
                    $rmaModel = $this->_objectManager->create('Magento\Rma\Model\Rma');
                    $rmaData = array(
                        'status' => \Magento\Rma\Model\Rma\Source\Status::STATE_PENDING,
                        'date_requested' => $coreDate->gmtDate(),
                        'order_id' => $order->getId(),
                        'order_increment_id' => $order->getIncrementId(),
                        'store_id' => $order->getStoreId(),
                        'customer_id' => $order->getCustomerId(),
                        'order_date' => $order->getCreatedAt(),
                        'customer_name' => $order->getCustomerName(),
                        'customer_custom_email' => $post['customer_custom_email']
                    );
                    $result = $rmaModel->setData($rmaData)->saveRma($post);
                    if (!$result) {
                        $url = $urlModel->getUrl('*/*/create', array('order_id' => $orderId));
                        $this->getResponse()->setRedirect($this->_redirect->error($url));
                        return;
                    }
                    $result->sendNewRmaEmail();
                    if (isset($post['rma_comment']) && !empty($post['rma_comment'])) {
                        /** @var $statusHistory \Magento\Rma\Model\Rma\Status\History */
                        $statusHistory = $this->_objectManager->create('Magento\Rma\Model\Rma\Status\History');
                        $statusHistory->setRmaEntityId(
                            $rmaModel->getId()
                        )->setComment(
                            $post['rma_comment']
                        )->setIsVisibleOnFront(
                            true
                        )->setStatus(
                            $rmaModel->getStatus()
                        )->setCreatedAt(
                            $coreDate->gmtDate()
                        )->save();
                    }
                    $this->messageManager->addSuccess(__('You submitted Return #%1.', $rmaModel->getIncrementId()));
                    $this->getResponse()->setRedirect($this->_redirect->success($urlModel->getUrl('*/*/history')));
                    return;
                } catch (\Exception $e) {
                    $this->messageManager->addError(
                        __('We cannot create a new return transaction. Please try again later.')
                    );
                    $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
                }
            }
            $this->_view->loadLayout();
            $layout = $this->_view->getLayout();
            $layout->initMessages();
            $layout->getBlock('head')->setTitle(__('Create New Return'));
            if ($block = $this->_view->getLayout()->getBlock('customer.account.link.back')) {
                $block->setRefererUrl($this->_redirect->getRefererUrl());
            }
            $this->_view->renderLayout();
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
        $customerId = $this->_objectManager->get('Magento\Customer\Model\Session')->getCustomerId();
        if ($item->getId() && $item->getCustomerId() && $item->getCustomerId() == $customerId) {
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
            $entityId = (int)$this->getRequest()->getParam('entity_id');
        }
        if (!$entityId || !$this->_isEnabledOnFront()) {
            $this->_forward('noroute');
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
        $this->messageManager->addError($message);
        $this->_redirect('sales/order/history');
        return false;
    }

    /**
     * RMA view page
     *
     * @return void
     */
    public function viewAction()
    {
        if (!$this->_loadValidRma()) {
            $this->_redirect('*/*/history');
            return;
        }
        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->_objectManager->create(
            'Magento\Sales\Model\Order'
        )->load(
            $this->_coreRegistry->registry('current_rma')->getOrderId()
        );
        $this->_coreRegistry->register('current_order', $order);

        $this->_view->loadLayout();
        $layout = $this->_view->getLayout();
        $layout->initMessages();
        $layout->getBlock(
            'head'
        )->setTitle(
            __('Return #%1', $this->_coreRegistry->registry('current_rma')->getIncrementId())
        );

        $this->_view->renderLayout();
    }

    /**
     * View RMA for Order
     *
     * @return false|null
     */
    public function returnsAction()
    {
        $orderId = (int)$this->getRequest()->getParam('order_id');
        $customerId = $this->_objectManager->get('Magento\Customer\Model\Session')->getCustomerId();

        if (!$orderId || !$this->_isEnabledOnFront()) {
            $this->_forward('noroute');
            return false;
        }

        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);

        $availableStates = $this->_objectManager->get('Magento\Sales\Model\Order\Config')->getVisibleOnFrontStates();
        if ($order->getId() && $order->getCustomerId() && $order->getCustomerId() == $customerId && in_array(
            $order->getState(),
            $availableStates,
            $strict = true
        )
        ) {
            $this->_coreRegistry->register('current_order', $order);
        } else {
            $this->_redirect('*/*/history');
            return;
        }

        $this->_view->loadLayout();
        $layout = $this->_view->getLayout();
        $layout->initMessages();

        if ($navigationBlock = $layout->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('sales/order/history');
        }
        $this->_view->renderLayout();
    }

    /**
     * Add RMA comment action
     *
     * @return void
     */
    public function addCommentAction()
    {
        if ($this->_loadValidRma()) {
            try {
                $response = false;
                $comment = $this->getRequest()->getPost('comment');
                $comment = trim(strip_tags($comment));

                if (!empty($comment)) {
                    /** @var $dateModel \Magento\Framework\Stdlib\DateTime\DateTime */
                    $dateModel = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime');
                    /** @var $statusHistory \Magento\Rma\Model\Rma\Status\History */
                    $statusHistory = $this->_objectManager->create('Magento\Rma\Model\Rma\Status\History');
                    $result = $statusHistory->setRmaEntityId(
                        $this->_coreRegistry->registry('current_rma')->getEntityId()
                    )->setComment(
                        $comment
                    )->setIsVisibleOnFront(
                        true
                    )->setStatus(
                        $this->_coreRegistry->registry('current_rma')->getStatus()
                    )->setCreatedAt(
                        $dateModel->gmtDate()
                    )->save();
                    $result->setStoreId($this->_coreRegistry->registry('current_rma')->getStoreId());
                    $result->sendCustomerCommentEmail();
                } else {
                    throw new \Magento\Framework\Model\Exception(__('Please enter a valid message.'));
                }
            } catch (\Magento\Framework\Model\Exception $e) {
                $response = array('error' => true, 'message' => $e->getMessage());
            } catch (\Exception $e) {
                $response = array('error' => true, 'message' => __('Cannot add message.'));
            }
            if (is_array($response)) {
                $this->messageManager->addError($response['message']);
            }
            $this->_redirect('*/*/view', array('entity_id' => (int)$this->getRequest()->getParam('entity_id')));
            return;
        }
        return;
    }

    /**
     * Add Tracking Number action
     *
     * @return void
     */
    public function addLabelAction()
    {
        if ($this->_loadValidRma()) {
            try {
                $rma = $this->_coreRegistry->registry('current_rma');

                if (!$rma->isAvailableForPrintLabel()) {
                    throw new \Magento\Framework\Model\Exception(__('Shipping Labels are not allowed.'));
                }

                $response = false;
                $number = $this->getRequest()->getPost('number');
                $number = trim(strip_tags($number));
                $carrier = $this->getRequest()->getPost('carrier');
                $carriers = $this->_objectManager->get(
                    'Magento\Rma\Helper\Data'
                )->getShippingCarriers(
                    $rma->getStoreId()
                );

                if (!isset($carriers[$carrier])) {
                    throw new \Magento\Framework\Model\Exception(__('Please select a valid carrier.'));
                }

                if (empty($number)) {
                    throw new \Magento\Framework\Model\Exception(__('Please enter a valid tracking number.'));
                }

                /** @var $rmaShipping \Magento\Rma\Model\Shipping */
                $rmaShipping = $this->_objectManager->create('Magento\Rma\Model\Shipping');
                $rmaShipping->setRmaEntityId(
                    $rma->getEntityId()
                )->setTrackNumber(
                    $number
                )->setCarrierCode(
                    $carrier
                )->setCarrierTitle(
                    $carriers[$carrier]
                )->save();
            } catch (\Magento\Framework\Model\Exception $e) {
                $response = array('error' => true, 'message' => $e->getMessage());
            } catch (\Exception $e) {
                $response = array('error' => true, 'message' => __('We cannot add a label.'));
            }
        } else {
            $response = array('error' => true, 'message' => __('The wrong RMA was selected.'));
        }
        if (is_array($response)) {
            $this->_objectManager->get('Magento\Framework\Session\Generic')->setErrorMessage($response['message']);
        }

        $this->_view->addPageLayoutHandles();
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
        return;
    }

    /**
     * Delete Tracking Number action
     *
     * @return void
     */
    public function delLabelAction()
    {
        if ($this->_loadValidRma()) {
            try {
                $rma = $this->_coreRegistry->registry('current_rma');

                if (!$rma->isAvailableForPrintLabel()) {
                    throw new \Magento\Framework\Model\Exception(__('Shipping Labels are not allowed.'));
                }

                $response = false;
                $number = intval($this->getRequest()->getPost('number'));

                if (empty($number)) {
                    throw new \Magento\Framework\Model\Exception(__('Please enter a valid tracking number.'));
                }

                /** @var $trackingNumber \Magento\Rma\Model\Shipping */
                $trackingNumber = $this->_objectManager->create('Magento\Rma\Model\Shipping')->load($number);
                if ($trackingNumber->getRmaEntityId() !== $rma->getId()) {
                    throw new \Magento\Framework\Model\Exception(__('The wrong RMA was selected.'));
                }
                $trackingNumber->delete();
            } catch (\Magento\Framework\Model\Exception $e) {
                $response = array('error' => true, 'message' => $e->getMessage());
            } catch (\Exception $e) {
                $response = array('error' => true, 'message' => __('We cannot delete the label.'));
            }
        } else {
            $response = array('error' => true, 'message' => __('The wrong RMA was selected.'));
        }
        if (is_array($response)) {
            $this->_objectManager->get('Magento\Framework\Session\Generic')->setErrorMessage($response['message']);
        }

        $this->_view->addPageLayoutHandles();
        $this->_view->loadLayout(false)->renderLayout();
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

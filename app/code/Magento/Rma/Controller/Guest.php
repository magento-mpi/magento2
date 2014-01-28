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

class Guest extends \Magento\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * View all returns
     *
     * @return void
     */
    public function returnsAction()
    {
        if (!$this->_objectManager->get('Magento\Rma\Helper\Data')->isEnabled()
            || !$this->_objectManager->get('Magento\Sales\Helper\Guest')->loadValidOrder()) {
            $this->_forward('noroute');
            return;
        }
        $this->_view->loadLayout();
        $this->_objectManager->get('Magento\Sales\Helper\Guest')->getBreadcrumbs();
        $this->_view->renderLayout();
    }

    /**
     * Check order view availability
     *
     * @param   \Magento\Rma\Model\Rma $rma
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
     *
     * @return void
     */
    public function viewAction()
    {
        if (!$this->_loadValidRma()) {
            $this->_redirect('*/*/returns');
            return;
        }

        $this->_view->loadLayout();
        $this->_objectManager->get('Magento\Sales\Helper\Guest')->getBreadcrumbs();
        $this->_view->getLayout()
            ->getBlock('head')
            ->setTitle(__('Return #%1', $this->_coreRegistry->registry('current_rma')->getIncrementId()));
        $this->_view->renderLayout();
    }

    /**
     * Try to load valid rma by entity_id and register it
     *
     * @param int $entityId
     * @return bool
     */
    protected function _loadValidRma($entityId = null)
    {
        if (!$this->_objectManager->get('Magento\Rma\Helper\Data')->isEnabled() ||
            !$this->_objectManager->get('Magento\Sales\Helper\Guest')->loadValidOrder()) {
            return;
        }

        if (null === $entityId) {
            $entityId = (int) $this->getRequest()->getParam('entity_id');
        }

        if (!$entityId) {
            $this->_forward('noroute');
            return false;
        }
        /** @var $rma \Magento\Rma\Model\Rma */
        $rma = $this->_objectManager->create('Magento\Rma\Model\Rma')->load($entityId);

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
     *
     * @return void
     */
    public function createAction()
    {
        if (!$this->_objectManager->get('Magento\Sales\Helper\Guest')->loadValidOrder()) {
            return;
        }
        $order      = $this->_coreRegistry->registry('current_order');
        $orderId    = $order->getId();
        if (!$this->_loadOrderItems($orderId)) {
            return;
        }

        $post = $this->getRequest()->getPost();
        /** @var \Magento\Core\Model\Date $coreDate */
        $coreDate = $this->_objectManager->get('Magento\Core\Model\Date');
        if (($post) && !empty($post['items'])) {
            try {
                /** @var $urlModel \Magento\UrlInterface */
                $urlModel = $this->_objectManager->get('Magento\UrlInterface');
                /** @var $rmaModel \Magento\Rma\Model\Rma */
                $rmaModel = $this->_objectManager->create('Magento\Rma\Model\Rma');
                $rmaData = array(
                    'status'                => \Magento\Rma\Model\Rma\Source\Status::STATE_PENDING,
                    'date_requested'        => $coreDate->gmtDate(),
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
                    $url = $urlModel->getUrl('*/*/create', array('order_id'  => $orderId));
                    $this->getResponse()->setRedirect($this->_redirect->error($url));
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
                        ->setCreatedAt($coreDate->gmtDate())
                        ->save();
                }
                $this->messageManager->addSuccess(
                    __('You submitted Return #%1.', $rmaModel->getIncrementId())
                );
                $url = $urlModel->getUrl('*/*/returns');
                $this->getResponse()->setRedirect($this->_redirect->success($url));
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We cannot create a new return transaction. Please try again later.')
                );
                $this->_objectManager->get('Magento\Logger')->logException($e);
            }
        }
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->getLayout()->getBlock('head')->setTitle(__('Create New Return'));
        if ($block = $this->_view->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->renderLayout();
    }

    /**
     * Try to load valid collection of ordered items
     *
     * @param int $orderId
     * @return bool
     */
    protected function _loadOrderItems($orderId)
    {
        if ($this->_objectManager->get('Magento\Rma\Helper\Data')->canCreateRma($orderId)) {
            return true;
        }

        $incrementId = $this->_coreRegistry->registry('current_order')->getIncrementId();
        $message = __('We cannot create a return transaction for order #%1.', $incrementId);
        $this->messageManager->addError($message);
        $this->_redirect('sales/order/history');
        return false;
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
                $response   = false;
                $comment    = $this->getRequest()->getPost('comment');
                $comment    = trim(strip_tags($comment));

                if (!empty($comment)) {
                    /** @var $statusHistory \Magento\Rma\Model\Rma\Status\History */
                    $statusHistory = $this->_objectManager->create('Magento\Rma\Model\Rma\Status\History');
                    $result = $statusHistory
                        ->setRmaEntityId($this->_coreRegistry->registry('current_rma')->getEntityId())
                        ->setComment($comment)
                        ->setIsVisibleOnFront(true)
                        ->setStatus($this->_coreRegistry->registry('current_rma')->getStatus())
                        ->setCreatedAt($this->_objectManager->get('Magento\Core\Model\Date')->gmtDate())
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
                    'message'   => __('We cannot add a message.')
                );
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
                    throw new \Magento\Core\Exception(__('Shipping Labels are not allowed.'));
                }

                $response   = false;
                $number    = $this->getRequest()->getPost('number');
                $number    = trim(strip_tags($number));
                $carrier   = $this->getRequest()->getPost('carrier');
                $carriers  = $this->_objectManager->get('Magento\Rma\Helper\Data')
                    ->getShippingCarriers($rma->getStoreId());

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
            $this->_objectManager->get('Magento\Core\Model\Session')->setErrorMessage($response['message']);
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
                    throw new \Magento\Core\Exception(__('Shipping Labels are not allowed.'));
                }

                $response   = false;
                $number    = intval($this->getRequest()->getPost('number'));

                if (empty($number)) {
                    throw new \Magento\Core\Exception(__('Please enter a valid tracking number.'));
                }
                /** @var $trackingNumber \Magento\Rma\Model\Shipping */
                $trackingNumber = $this->_objectManager->create('Magento\Rma\Model\Shipping')
                    ->load($number);
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
            $this->_objectManager->get('Magento\Core\Model\Session')->setErrorMessage($response['message']);
        }

        $this->_view->addPageLayoutHandles();
        $this->_view->loadLayout(false)
            ->renderLayout();
        return;
    }


}

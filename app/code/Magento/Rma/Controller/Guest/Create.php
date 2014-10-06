<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Guest;

use \Magento\Rma\Model\Rma;

class Create extends \Magento\Rma\Controller\Guest
{
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
     * Customer create new return
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->_objectManager->get('Magento\Sales\Helper\Guest')
            ->loadValidOrder($this->_request, $this->_response)
        ) {
            return;
        }
        $order = $this->_coreRegistry->registry('current_order');
        $orderId = $order->getId();
        if (!$this->_loadOrderItems($orderId)) {
            return;
        }

        $post = $this->getRequest()->getPost();
        /** @var \Magento\Framework\Stdlib\DateTime\DateTime $coreDate */
        $coreDate = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime');
        if ($post && !empty($post['items'])) {
            try {
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
                    $url = $this->_url->getUrl('*/*/create', array('order_id' => $orderId));
                    $this->getResponse()->setRedirect($this->_redirect->error($url));
                    return;
                }
                /** @var $statusHistory \Magento\Rma\Model\Rma\Status\History */
                $statusHistory = $this->_objectManager->create('Magento\Rma\Model\Rma\Status\History');
                $statusHistory->setRma($result);
                $statusHistory->sendNewRmaEmail();
                $statusHistory->saveSystemComment();
                if (isset($post['rma_comment']) && !empty($post['rma_comment'])) {
                    /** @var $statusHistory \Magento\Rma\Model\Rma\Status\History */
                    $comment = $this->_objectManager->create('Magento\Rma\Model\Rma\Status\History');
                    $comment->setRma($result);
                    $comment->saveComment($post['rma_comment'], true, false);
                }
                $this->messageManager->addSuccess(__('You submitted Return #%1.', $rmaModel->getIncrementId()));
                $url = $this->_url->getUrl('*/*/returns');
                $this->getResponse()->setRedirect($this->_redirect->success($url));
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We cannot create a new return transaction. Please try again later.')
                );
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            }
        }
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->getPage()->getConfig()->setTitle(__('Create New Return'));
        if ($block = $this->_view->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->renderLayout();
    }
}

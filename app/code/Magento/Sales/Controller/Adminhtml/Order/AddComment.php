<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order;

use \Magento\Backend\App\Action;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;

class AddComment extends \Magento\Sales\Controller\Adminhtml\Order
{
    /**
     * Add order comment action
     *
     * @return void
     */
    public function execute()
    {
        $order = $this->_initOrder();
        if ($order) {
            try {
                $response = false;
                $data = $this->getRequest()->getPost('history');
                if (empty($data['comment']) && $data['status'] == $order->getDataByKey('status')) {
                    throw new \Magento\Framework\Model\Exception(__('Comment text cannot be empty.'));
                }

                $notify = isset($data['is_customer_notified']) ? $data['is_customer_notified'] : false;
                $visible = isset($data['is_visible_on_front']) ? $data['is_visible_on_front'] : false;

                $history = $order->addStatusHistoryComment($data['comment'], $data['status']);
                $history->setIsVisibleOnFront($visible);
                $history->setIsCustomerNotified($notify);
                $history->save();

                $comment = trim(strip_tags($data['comment']));

                $order->save();
                /** @var OrderCommentSender $orderCommentSender */
                $orderCommentSender = $this->_objectManager
                    ->create('Magento\Sales\Model\Order\Email\Sender\OrderCommentSender');

                $orderCommentSender->send($order, $notify, $comment);

                $this->_view->loadLayout('empty');
                $this->_view->renderLayout();
            } catch (\Magento\Framework\Model\Exception $e) {
                $response = array('error' => true, 'message' => $e->getMessage());
            } catch (\Exception $e) {
                $response = array('error' => true, 'message' => __('We cannot add order history.'));
            }
            if (is_array($response)) {
                $response = $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($response);
                $this->getResponse()->representJson($response);
            }
        }
    }
}

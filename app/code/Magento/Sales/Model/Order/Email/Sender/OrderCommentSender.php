<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Order\Email\Sender;

use Magento\Sales\Model\Order\Email\NotifySender;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\Order\Email\Container\OrderCommentIdentity;

class OrderCommentSender extends NotifySender
{
    /**
     * Send email to customer
     *
     * @param Order $order
     * @param bool $notify
     * @param string $comment
     * @return bool
     */
    public function send(Order $order, $notify = true, $comment = '')
    {
        $this->templateContainer->setTemplateVars(
            [
                'order' => $order,
                'comment' => $comment,
                'billing' => $order->getBillingAddress(),
                'store' => $order->getStore()
            ]
        );
        return $this->checkAndSend($order, $notify);
    }
}

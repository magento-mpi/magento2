<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class OrderLoadAfter
{
    /**
     * Set forced can creditmemo flag if refunded amount less then invoiced amount of reward points
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getOrder();
        if ($order->canUnhold()) {
            return $this;
        }
        if ($order->isCanceled() || $order->getState() === \Magento\Sales\Model\Order::STATE_CLOSED) {
            return $this;
        }
        if ($order->getBaseRwrdCrrncyAmtInvoiced() - $order->getBaseRwrdCrrncyAmntRefnded() > 0) {
            $order->setForcedCanCreditmemo(true);
        }
        return $this;
    }
}

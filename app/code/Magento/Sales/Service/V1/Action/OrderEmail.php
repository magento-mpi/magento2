<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Model\OrderRepository;

/**
 * Class OrderEmail
 */
class OrderEmail
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var \Magento\Sales\Model\OrderNotifier
     */
    protected $notifier;

    /**
     * @param OrderRepository $orderRepository
     * @param \Magento\Sales\Model\OrderNotifier $notifier
     */
    public function __construct(
        OrderRepository $orderRepository,
        \Magento\Sales\Model\OrderNotifier $notifier
    ) {
        $this->orderRepository = $orderRepository;
        $this->notifier = $notifier;
    }

    /**
     * Invoke notifyUser service
     *
     * @param int $id
     * @return bool
     */
    public function invoke($id)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->orderRepository->get($id);
        return $this->notifier->notify($order);
    }
}

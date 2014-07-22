<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Model\OrderRepository;

/**
 * Class OrderNotifyUser
 */
class OrderNotifyUser implements OrderNotifyUserInterface
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var \Magento\Sales\Model\Notifier
     */
    protected $notifier;

    /**
     * @param OrderRepository $orderRepository
     * @param \Magento\Sales\Model\Notifier $notifier
     */
    public function __construct(
        OrderRepository $orderRepository,
        \Magento\Sales\Model\Notifier $notifier
    ) {
        $this->orderRepository = $orderRepository;
        $this->notifier = $notifier;
    }

    /**
     * Invoke notifyUser service
     *
     * @param int $id
     * @return \Magento\Framework\Service\Data\AbstractObject
     */
    public function invoke($id)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->orderRepository->get($id);
        return $this->notifier->notify($order);
    }
}

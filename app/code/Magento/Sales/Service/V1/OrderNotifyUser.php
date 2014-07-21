<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Service\V1\Data\OrderMapper;

/**
 * Class OrderNotifyUser
 */
class OrderNotifyUser
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
     * Invoke getOrder service
     *
     * @param $id
     * @return \Magento\Framework\Service\Data\AbstractObject
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function invoke($id)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->orderRepository->get($id);
        return $this->notifier->notify($order);
    }
}

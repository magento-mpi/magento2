<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\Order\Status\HistoryConverter;

/**
 * Class OrderStatusHistoryAdd
 * @package Magento\Sales\Service\V1
 */
class OrderStatusHistoryAdd implements OrderStatusHistoryAddInterface
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var HistoryConverter
     */
    protected $historyConverter;

    /**
     * @param OrderRepository $orderRepository
     * @param HistoryConverter $historyConverter
     */
    public function __construct(
        OrderRepository $orderRepository,
        HistoryConverter $historyConverter
    ) {
        $this->orderRepository = $orderRepository;
        $this->historyConverter = $historyConverter;
    }

    /**
     * Invoke service
     *
     * @param int $id
     * @param \Magento\Sales\Service\V1\Data\OrderStatusHistory $statusHistory
     * @return int|mixed
     */
    public function invoke($id, $statusHistory)
    {
        $order = $this->orderRepository->get($id);
        $order->addStatusHistory($this->historyConverter->getModel($statusHistory));
        return $order->save();
    }
}

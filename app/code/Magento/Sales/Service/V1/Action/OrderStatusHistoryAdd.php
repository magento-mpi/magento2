<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\Order\Status\HistoryConverter;
use Magento\Sales\Service\V1\Data\OrderStatusHistory;

/**
 * Class OrderStatusHistoryAdd
 * @package Magento\Sales\Service\V1
 */
class OrderStatusHistoryAdd
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
     * @return bool
     */
    public function invoke($id, OrderStatusHistory $statusHistory)
    {
        $order = $this->orderRepository->get($id);
        $order->addStatusHistory($this->historyConverter->getModel($statusHistory));
        $order->save();
        return true;
    }
}

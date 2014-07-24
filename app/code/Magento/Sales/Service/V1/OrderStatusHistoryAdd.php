<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Service\V1\Data\OrderStatusHistoryConverter;

/**
 * Class OrderCommentsAdd
 * @package Magento\Sales\Service\V1
 */
class OrderStatusHistoryAdd implements OrderStatusHistoryAddInterface
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var OrderStatusHistoryConverter
     */
    protected $orderStatusHistoryConverter;

    /**
     * @param OrderRepository $orderRepository
     * @param OrderStatusHistoryConverter $orderMapper
     */
    public function __construct(
        OrderRepository $orderRepository,
        OrderStatusHistoryConverter $orderMapper
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderStatusHistoryMapper = $orderMapper;
    }

    /**
     * Invoke getOrder service
     *
     * @param int $id
     * @param \Magento\Sales\Service\V1\Data\OrderStatusHistory $statusHistory
     * @return void
     */
    public function invoke($id, $statusHistory)
    {
        $statusHistoryModel = $this->orderStatusHistoryConverter->getModel($statusHistory);
        $this->orderRepository->get($id)->addStatusHistory($statusHistoryModel);
    }
}

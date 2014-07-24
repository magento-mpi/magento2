<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Service\V1\Data\OrderStatusHistoryMapper;

/**
 * Class OrderCommentsAdd
 * @package Magento\Sales\Service\V1
 */
class OrderCommentsConverter
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var OrderStatusHistoryMapper
     */
    protected $orderStatusHistoryMapper;

    /**
     * @param OrderRepository $orderRepository
     * @param OrderStatusHistoryMapper $orderMapper
     */
    public function __construct(
        OrderRepository $orderRepository,
        OrderSatusHistoryMapper $orderMapper
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderStatusHistoryMapper = $orderMapper;
    }

    /**
     * Invoke getOrder service
     *
     * @param int $id
     * @param \Magento\Sales\Service\V1\Data\OrderStatusHistory
     * @return \Magento\Framework\Service\Data\AbstractObject
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function invoke($id, $statusHistoryDo)
    {
        $statusHistory = $this->orderStatusHistoryMapper->get
        return $this->orderRepository->get($id)->addStatusHistory($statusHistoryDo);
    }
}

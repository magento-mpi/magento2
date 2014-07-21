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
use Magento\Framework\Service\V1\Data\SearchCriteria;

/**
 * Class OrderList
 */
class OrderList
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var OrderMapper
     */
    protected $orderMapper;

    /**
     * @param OrderRepository $orderRepository
     * @param OrderMapper $orderMapper
     */
    public function __construct(
        OrderRepository $orderRepository,
        OrderMapper $orderMapper
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderMapper = $orderMapper;
    }

    /**
     * Invoke OrderList service
     *
     * @param $criteria
     *
     * @return \Magento\Framework\Service\Data\AbstractObject
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function invoke(SearchCriteria $criteria)
    {
        $orders = [];
        foreach($this->orderRepository->find($criteria) as $order)
        {
            $orders[] = $this->orderMapper->extractDto($order);
        }
        return $orders;
    }
}

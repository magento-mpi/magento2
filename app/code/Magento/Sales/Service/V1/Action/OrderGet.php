<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Service\V1\Data\OrderMapper;

/**
 * Class OrderGet
 */
class OrderGet
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
     * Invoke getOrder service
     *
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\Order
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function invoke($id)
    {
        return $this->orderMapper->extractDto($this->orderRepository->get($id));
    }
}

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
 * Class OrderGet
 */
class OrderGet implements OrderGetInterface
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
     * @return \Magento\Framework\Service\Data\AbstractObject
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function invoke($id)
    {
        return $this->orderMapper->extractDto($this->orderRepository->get($id));
    }
}

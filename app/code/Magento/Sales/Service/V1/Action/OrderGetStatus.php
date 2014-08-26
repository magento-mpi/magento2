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
 * Class OrderGetStatus
 */
class OrderGetStatus
{
    /**
     * Order repository
     *
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Retrieve order status by id
     *
     * @param int $id
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function invoke($id)
    {
        return $this->orderRepository->get($id)->getStatus();
    }
}

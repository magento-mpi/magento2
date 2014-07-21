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
 * Class OrderCancel
 */
class OrderCancel
{
    /**
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
     * Invoke getOrder service
     *
     * @param int $id
     * @return \Magento\Framework\Service\Data\AbstractObject
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function invoke($id)
    {
        return (bool)$this->orderRepository->get($id)->cancel();
    }
}

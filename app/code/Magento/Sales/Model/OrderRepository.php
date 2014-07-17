<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model;

use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class OrderRepository
 */
class OrderRepository
{
    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var Order[]
     */
    protected $registry;

    /**
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        OrderFactory $orderFactory
    ) {
        $this->orderFactory = $orderFactory;
    }

    /**
     * Returns Order model
     *
     * @param $orderId
     * @return Order
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($orderId)
    {
        if (!$orderId) {
            throw new NoSuchEntityException('Requested product doesn\'t exist');
        }
        if (!isset($this->registry[$orderId])) {
            $this->registry[$orderId] = $this->orderFactory->create()->load($orderId);
        }
        return $this->registry[$orderId];
    }
}

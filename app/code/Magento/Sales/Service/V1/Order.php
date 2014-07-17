<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Service\V1\Order;

class Order {

    private $orderFactory;

    private $orderMapper;

    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Service\V1\Data\OrderMapper $orderMapper
    ) {
        $this->orderFactory = $orderFactory;
        $this->orderMapper = $orderMapper;
    }

    public function salesOrderGet($id)
    {
        $order = $this->orderFactory->create()->load($id);
        return $this->orderMapper->toData($order);
    }
} 
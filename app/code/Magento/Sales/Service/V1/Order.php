<?php
/**
 * Created by PhpStorm.
 * User: sivashchenko
 * Date: 7/14/14
 * Time: 5:37 PM
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
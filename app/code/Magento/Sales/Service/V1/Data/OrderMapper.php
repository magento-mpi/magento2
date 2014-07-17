<?php
/**
 * Created by PhpStorm.
 * User: sivashchenko
 * Date: 7/14/14
 * Time: 6:30 PM
 */

namespace Magento\Sales\Service\V1\Data;


class OrderMapper {

    private $orderBuilder;

    private $objectManager;

    public function __construct(\Magento\Framework\App\ObjectManager $objectManager, OrderBuilder $orderBuilder)
    {
        $this->objectManager = $objectManager;
        $this->orderBuilder = $orderBuilder;
    }

    public function toModel(Order $orderDataObject)
    {
        $order = $this->objectManager->create('Magento\Sales\Model\Order');
        if ($orderDataObject->getId()) {
            $order->load($orderDataObject->getId());
        }
        $order->setData($orderDataObject->getData());
        return $order;
    }

    public function toData($orderModel)
    {
        return $this->objectManager->create('Magento\Sales\Service\V1\Data\Order', ['data' => $orderModel->getData()]);
    }
} 
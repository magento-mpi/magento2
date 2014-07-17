<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Service\V1;

use Magento\Sales\Service\V1\Data\OrderBuilder;
use Magento\Sales\Service\V1\Data\Order;

class OrderConverter
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var OrderBuilder
     */
    protected $orderBuilder;

    /**
     * @param OrderRepository $orderRepository
     * @param OrderBuilder $orderBuilder
     */
    public function __construct(
        OrderRepository $orderRepository,
        OrderBuilder $orderBuilder
    ){
        $this->orderRepository = $orderRepository;
        $this->orderBuilder = $orderBuilder;
    }


    /**
     * Convert a order model to a order data entity
     *
     * @param \Magento\Sales\Model\Order $productModel
     * @return \Magento\Sales\Service\V1\Data\Order
     */
    public function createOrderDataFromModel(\Magento\Sales\Model\Order $productModel)
    {
        $orderDto = $this->orderBuilder->populateWithArray($productModel->getData())->create();
        $orderDto['order_item'] = $orderItemDto;
    }

    /**
     * Convert from DataObject to Model
     *
     * @param Order $orderDataObject
     * @return \Magento\Sales\Model\Order
     */
    public function toModel(Order $orderDataObject)
    {
        $order = null;
        if ($orderDataObject->getId()) {
            $order = $this->orderRepository->get($orderDataObject->getId());
            $order->setData($orderDataObject->getData());
        }

        return $order;
    }
}

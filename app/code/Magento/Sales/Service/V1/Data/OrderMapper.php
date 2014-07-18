<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Service\V1\Data;

use Magento\Sales\Model\Order;

class OrderMapper
{
    /**
     * @var OrderBuilder
     */
    protected $orderBuilder;

    /**
     * @param OrderBuilder $orderBuilder
     */
    public function __construct(
        \Magento\Sales\Service\V1\Data\OrderBuilder $orderBuilder
    ) {
        $this->orderBuilder = $orderBuilder;
    }

    /**
     * @param Order $order
     * @return \Magento\Framework\Service\Data\AbstractObject
     */
    public function extractDto(Order $order)
    {
        $this->orderBuilder->populateWithArray($order->getData());
        return $this->orderBuilder->create();
    }
}


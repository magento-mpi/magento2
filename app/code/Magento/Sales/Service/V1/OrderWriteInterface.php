<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Service\V1\Data\OrderAddress;
use Magento\Sales\Service\V1\Data\OrderStatusHistory;

interface OrderWriteInterface
{
    /**
     * @param \Magento\Sales\Service\V1\Data\OrderAddress $orderAddress
     * @return bool
     */
    public function addressUpdate(OrderAddress $orderAddress);

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function cancel($id);

    /**
     * @param int $id
     * @return bool
     */
    public function email($id);

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function hold($id);

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function unHold($id);

    /**
     * @param int $id
     * @param \Magento\Sales\Service\V1\Data\OrderStatusHistory $statusHistory
     * @return bool
     */
    public function statusHistoryAdd($id, OrderStatusHistory $statusHistory);

    /**
     * Create an order
     *
     * @param \Magento\Sales\Service\V1\Data\Order $orderDataObject
     * @return bool
     * @throws \Exception
     */
    public function create(\Magento\Sales\Service\V1\Data\Order $orderDataObject);
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Service\V1\Action\OrderAddressUpdate;
use Magento\Sales\Service\V1\Action\OrderCancel;
use Magento\Sales\Service\V1\Action\OrderEmail;
use Magento\Sales\Service\V1\Action\OrderHold;
use Magento\Sales\Service\V1\Action\OrderUnHold;
use Magento\Sales\Service\V1\Action\OrderStatusHistoryAdd;
use Magento\Sales\Service\V1\Data\OrderAddress;
use Magento\Sales\Service\V1\Data\OrderStatusHistory;

/**
 * Class OrderWrite
 */
class OrderWrite implements OrderWriteInterface
{
    /**
     * @var OrderAddressUpdate
     */
    protected $orderAddressUpdate;

    /**
     * @var OrderCancel
     */
    protected $orderCancel;

    /**
     * @var OrderEmail
     */
    protected $orderEmail;

    /**
     * @var OrderHold
     */
    protected $orderHold;

    /**
     * @var OrderUnHold
     */
    protected $orderUnHold;

    /**
     * @var OrderStatusHistoryAdd
     */
    protected $orderStatusHistoryAdd;

    /**
     * @param OrderAddressUpdate $orderAddressUpdate
     * @param OrderCancel $orderCancel
     * @param OrderEmail $orderEmail
     * @param OrderHold $orderHold
     * @param OrderUnHold $orderUnHold
     * @param OrderStatusHistoryAdd $orderStatusHistoryAdd
     */
    public function __construct(
        OrderAddressUpdate $orderAddressUpdate,
        OrderCancel $orderCancel,
        OrderEmail $orderEmail,
        OrderHold $orderHold,
        OrderUnHold $orderUnHold,
        OrderStatusHistoryAdd $orderStatusHistoryAdd
    ) {
        $this->orderAddressUpdate = $orderAddressUpdate;
        $this->orderCancel = $orderCancel;
        $this->orderEmail = $orderEmail;
        $this->orderHold = $orderHold;
        $this->orderUnHold = $orderUnHold;
        $this->orderStatusHistoryAdd = $orderStatusHistoryAdd;
    }

    /**
     * @param \Magento\Sales\Service\V1\Data\OrderAddress $orderAddress
     * @return bool
     */
    public function addressUpdate(OrderAddress $orderAddress)
    {
        return $this->orderAddressUpdate->invoke($orderAddress);
    }

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function cancel($id)
    {
        return $this->orderCancel->invoke($id);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function email($id)
    {
        return $this->orderEmail->invoke($id);
    }

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function hold($id)
    {
        return $this->orderHold->invoke($id);
    }

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function unHold($id)
    {
        return $this->orderUnHold->invoke($id);
    }

    /**
     * @param int $id
     * @param \Magento\Sales\Service\V1\Data\OrderStatusHistory $statusHistory
     * @return bool
     */
    public function statusHistoryAdd($id, OrderStatusHistory $statusHistory)
    {
        return $this->orderStatusHistoryAdd->invoke($id, $statusHistory);
    }
}

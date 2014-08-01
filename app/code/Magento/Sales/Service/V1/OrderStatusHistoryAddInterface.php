<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Service\V1\Data\OrderStatusHistory;

/**
 * Interface OrderCommentsAddInterface
 * @package Magento\Sales\Service\V1
 */
interface OrderStatusHistoryAddInterface
{
    /**
     * Invoke service
     *
     * @param int $id
     * @param OrderStatusHistory $statusHistory
     * @return int|mixed
     */
    public function invoke($id, OrderStatusHistory $statusHistory);
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

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
     * @param \Magento\Sales\Service\V1\Data\OrderStatusHistory $statusHistory
     * @return int|mixed
     */
    public function invoke($id, $statusHistory);
}

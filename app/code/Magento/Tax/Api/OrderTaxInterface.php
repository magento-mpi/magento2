<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Api;

/**
 * @see \Magento\Tax\Service\V1\OrderTaxServiceInterface
 */
interface OrderTaxInterface
{
    /**
     * Get taxes applied to an order
     *
     * @param int $orderId
     * @return \Magento\Tax\Data\OrderTaxDetailsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOrderTaxDetails($orderId);
}

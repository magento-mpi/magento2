<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

/**
 * Interface OrderAddressUpdate
 * @package Magento\Sales\Service\V1
 */
interface OrderAddressUpdateInterface
{
    /**
     * Invoke orderAddressUpdate service
     *
     * @param \Magento\Sales\Service\V1\Data\OrderAddress $orderAddress
     * @return int|mixed
     * @throws void
     */
    public function invoke(\Magento\Sales\Service\V1\Data\OrderAddress $orderAddress);
}

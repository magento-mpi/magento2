<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\ShippingMethod;


interface WriteServiceInterface
{
    /**
     * Set shipping method and carrier for the specified cart
     *
     * @param int $cartId
     * @param string $carrierId
     * @param string $methodId
     * @return bool
     */
    public function setMethod($cartId, $carrierId, $methodId);
}

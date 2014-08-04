<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Cart\ShippingMethod;


interface WriteServiceInterface
{
    /**
     * @param string $carrierId
     * @param string $methodId
     * @return bool
     */
    public function method($carrierId, $methodId);
}
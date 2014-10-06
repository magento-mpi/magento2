<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\ShippingMethod;

/**
 * Interface to choose shipping method for cart address
 */
interface WriteServiceInterface
{
    /**
     * Set shipping method and carrier for the specified cart
     *
     * @param int $cartId
     * @param string $carrierCode
     * @param string $methodCode
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @return bool
     */
    public function setMethod($cartId, $carrierCode, $methodCode);
}

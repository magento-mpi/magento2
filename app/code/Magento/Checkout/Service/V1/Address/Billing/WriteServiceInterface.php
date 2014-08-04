<?php
/**
 * Quote billing address service
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Address\Billing;

interface WriteServiceInterface
{
    /**
     * Assign billing address to cart
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @param int $cartId
     * @param \Magento\Checkout\Service\V1\Data\Cart\Address $addressData
     * @return int
     */
    public function setAddress($cartId, $addressData);
}

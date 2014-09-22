<?php
/**
 * Quote shipping address service
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Address\Shipping;

interface ReadServiceInterface
{
    /**
     * Get shipping address of the quote
     *
     * @param int $cartId
     * @return \Magento\Checkout\Service\V1\Data\Cart\Address
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAddress($cartId);
}

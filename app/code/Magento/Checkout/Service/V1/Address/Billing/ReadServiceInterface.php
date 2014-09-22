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

interface ReadServiceInterface
{
    /**
     * Get billing address of the quote
     *
     * @param int $cartId
     * @return \Magento\Checkout\Service\V1\Data\Cart\Address
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAddress($cartId);
}

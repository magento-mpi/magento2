<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\ShippingMethod;

/**
 * Quote shipping method read service interface.
 */
interface ReadServiceInterface
{
    /**
     * Returns selected shipping method for a specified quote.
     *
     * @param int $cartId The shopping cart ID.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified shopping cart does not exist.
     * @throws \Magento\Framework\Exception\StateException The shipping address is not set.
     */
    public function getMethod($cartId);

    /**
     * Lists applicable shipping methods for a specified quote.
     *
     * @param int $cartId The shopping cart ID.
     * @return \Magento\Checkout\Service\V1\Data\Cart\ShippingMethod[] An array of shipping methods.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified quote does not exist.
     * @throws \Magento\Framework\Exception\StateException The shipping address is not set.
     */
    public function getList($cartId);
}

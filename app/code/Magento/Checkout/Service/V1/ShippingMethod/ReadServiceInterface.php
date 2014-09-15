<?php
/**
 * Quote shipping method read service
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\ShippingMethod;

interface ReadServiceInterface
{
    /**
     * Get selected shipping method of the quote
     *
     * @param int $cartId
     * @return \Magento\Checkout\Service\V1\Data\Cart\ShippingMethod
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function getMethod($cartId);


    /**
     * Get list of applicable shipping methods for quote
     *
     * @param int $cartId
     * @return \Magento\Checkout\Service\V1\Data\Cart\ShippingMethod[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function getList($cartId);
}

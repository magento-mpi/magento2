<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\PaymentMethod;

interface ReadServiceInterface
{
    /**
     * Get list of payment methods
     *
     * @param int $cartId
     * @return \Magento\Checkout\Service\V1\Data\Cart\PaymentMethod
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPayment($cartId);

    /**
     * Get the list of available payment methods for a shopping cart
     *
     * @param int $cartId
     * @return \Magento\Checkout\Service\V1\Data\PaymentMethod[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList($cartId);
}

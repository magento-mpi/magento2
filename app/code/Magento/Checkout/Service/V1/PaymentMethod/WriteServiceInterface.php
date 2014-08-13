<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\PaymentMethod;

interface WriteServiceInterface
{
    /**
     * Add payment method to list of selected for cart
     *
     * @param \Magento\Checkout\Service\V1\Data\Cart\PaymentMethod $method
     * @param int $cartId
     * @return int
     * @throws \Magento\Framework\Exception\State\InvalidTransitionException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function set(\Magento\Checkout\Service\V1\Data\Cart\PaymentMethod $method, $cartId);
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Coupon;

/**
 * Interface ReadServiceInterface
 */
interface ReadServiceInterface
{
    /**
     * Retrieve information about coupon in cart
     *
     * @param int $cartId
     * @return \Magento\Checkout\Service\V1\Data\Cart\Coupon
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($cartId);
}

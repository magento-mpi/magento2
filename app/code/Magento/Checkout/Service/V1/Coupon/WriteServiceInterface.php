<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Coupon;

interface WriteServiceInterface
{
    /**
     * Add coupon by code to cart
     *
     * @param int $cartId
     * @param \Magento\Checkout\Service\V1\Data\Cart\Coupon $couponCodeData
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function set($cartId, \Magento\Checkout\Service\V1\Data\Cart\Coupon $couponCodeData);

    /**
     * Delete coupon from cart
     *
     * @param int $cartId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete($cartId);
}

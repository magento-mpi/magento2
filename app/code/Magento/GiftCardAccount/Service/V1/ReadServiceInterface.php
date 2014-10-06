<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Service\V1;

/**
 * Interface ReadServiceInterface
 */
interface ReadServiceInterface
{
    /**
     * Retrieve information about coupon in cart
     *
     * @param int $cartId
     * @return \Magento\GiftCardAccount\Service\V1\Data\Cart\GiftCardAccount
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList($cartId);
}

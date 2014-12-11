<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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

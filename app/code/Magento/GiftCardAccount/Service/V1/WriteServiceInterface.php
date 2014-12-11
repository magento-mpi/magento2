<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftCardAccount\Service\V1;

/**
 * Interface WriteServiceInterface
 */
interface WriteServiceInterface
{
    /**
     * Add gift card by code to cart
     *
     * @param int $cartId
     * @param \Magento\GiftCardAccount\Service\V1\Data\Cart\GiftCardAccount $giftCardAccountData
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function set($cartId, \Magento\GiftCardAccount\Service\V1\Data\Cart\GiftCardAccount $giftCardAccountData);

    /**
     * Delete gift card by code from cart
     *
     * @param int $cartId
     * @param string $giftCardCode
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException.php
     */
    public function delete($cartId, $giftCardCode);
}

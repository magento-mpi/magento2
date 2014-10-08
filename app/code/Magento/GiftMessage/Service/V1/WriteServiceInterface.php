<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftMessage\Service\V1;

/**
 * Gift message write service interface.
 */
interface WriteServiceInterface
{
    /**
     * Sets the gift message for the entire order.
     *
     * @param int $cartId The shopping cart ID.
     * @param Data\Message $giftMessage The gift message.
     * @return bool
     * @throws \Magento\Framework\Exception\InputException Gift messages are not applicable for empty carts.
     * @throws \Magento\Framework\Exception\State\InvalidTransitionException Gift messages are not applicable for virtual products.
     */
    public function setForQuote($cartId, \Magento\GiftMessage\Service\V1\Data\Message $giftMessage);

    /**
     * Sets the gift message for a specified item.
     *
     * @param int $cartId The shopping cart ID.
     * @param Data\Message $giftMessage The gift message.
     * @param int $itemId The item ID.
     * @return bool
     * @throws \Magento\Framework\Exception\State\InvalidTransitionException Gift messages are not applicable for virtual products.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified item does not exist in the specified cart.
     */
    public function setForItem($cartId, \Magento\GiftMessage\Service\V1\Data\Message $giftMessage, $itemId);
}

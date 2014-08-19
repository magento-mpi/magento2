<?php
/**
 * Quote shipping method read service
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftMessage\Service\V1;

interface WriteServiceInterface
{
    /**
     * Set gift message for the entire order
     *
     * @param int $cartId
     * @param \Magento\GiftMessage\Service\V1\Data\Message $giftMessage
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\State\InvalidTransitionException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function setForQuote($cartId, \Magento\GiftMessage\Service\V1\Data\Message $giftMessage);

    /**
     * Set gift message for the item
     *
     * @param int $cartId
     * @param \Magento\GiftMessage\Service\V1\Data\Message $giftMessage
     * @param int $itemId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\State\InvalidTransitionException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function setForItem($cartId, \Magento\GiftMessage\Service\V1\Data\Message $giftMessage, $itemId);
}

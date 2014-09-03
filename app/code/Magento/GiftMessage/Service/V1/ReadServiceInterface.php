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

interface ReadServiceInterface
{
    /**
     * Get gift message for order
     *
     * @param int $cartId
     * @return \Magento\GiftMessage\Service\V1\Data\Message
     */
    public function get($cartId);

    /**
     * Get gift message for item
     *
     * @param int $cartId
     * @param int $itemId
     * @return \Magento\GiftMessage\Service\V1\Data\Message
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getItemMessage($cartId, $itemId);
}

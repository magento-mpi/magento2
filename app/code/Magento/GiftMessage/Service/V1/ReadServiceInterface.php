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
     * @param int $cartId
     * @return \Magento\GiftMessage\Service\V1\Data\Message
     */
    public function get($cartId);

    /**
     * @param int $cartId
     * @param string $itemSku
     * @return \Magento\GiftMessage\Service\V1\Data\Message
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getItemMessage($cartId, $itemSku);
}

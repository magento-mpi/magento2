<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Item;

interface WriteServiceInterface
{
    /**
     * @param int $cartId
     * @param \Magento\Checkout\Service\V1\Data\Cart\Item $data
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function addItem($cartId, \Magento\Checkout\Service\V1\Data\Cart\Item $data);
}

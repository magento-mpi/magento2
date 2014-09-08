<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Item;

interface ReadServiceInterface
{
    /**
     * Get items assigned to a cart
     *
     * @param int $cartId
     * @return \Magento\Checkout\Service\V1\Data\Cart\Item[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList($cartId);
}

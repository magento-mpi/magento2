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
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function addItem($cartId, \Magento\Checkout\Service\V1\Data\Cart\Item $data);

    /**
     * @param int $cartId
     * @param int $itemId
     * @param \Magento\Checkout\Service\V1\Data\Cart\Item $data
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function updateItem($cartId, $itemId, \Magento\Checkout\Service\V1\Data\Cart\Item $data);

    /**
     * @param int $cartId
     * @param int $itemId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function removeItem($cartId, $itemId);
}

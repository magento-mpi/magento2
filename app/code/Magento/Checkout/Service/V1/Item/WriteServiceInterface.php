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
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function addItem($cartId, \Magento\Checkout\Service\V1\Data\Cart\Item $data);

    /**
     * @param int $cartId
     * @param string $itemSku
     * @param \Magento\Checkout\Service\V1\Data\Cart\Item $data
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function updateItem($cartId, $itemSku, \Magento\Checkout\Service\V1\Data\Cart\Item $data);

    /**
     * @param int $cartId
     * @param string $itemSku
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function removeItem($cartId, $itemSku);
}

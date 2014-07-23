<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Item;

use Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Checkout\Service\V1\Data\Cart\ItemBuilder as ItemBuilder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;

class WriteService implements WriteServiceInterface
{
    /**
     * @var \Magento\Checkout\Service\V1\QuoteLoader
     */
    protected $quoteLoader;

    /**
     * @var ItemBuilder
     */
    protected $itemBuilder;

    /**
     * @var \Magento\Catalog\Service\V1\Product\ProductLoader
     */
    protected $productLoader;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader
     * @param ItemBuilder $itemBuilder
     * @param \Magento\Catalog\Service\V1\Product\ProductLoader $productLoader
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader,
        ItemBuilder $itemBuilder,
        \Magento\Catalog\Service\V1\Product\ProductLoader $productLoader,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->quoteLoader = $quoteLoader;
        $this->itemBuilder = $itemBuilder;
        $this->productLoader = $productLoader;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function addItem($cartId, \Magento\Checkout\Service\V1\Data\Cart\Item $data)
    {
        $qty = $data->getQty();
        if (!is_double($qty) || $qty <= 0) {
            throw InputException::invalidFieldValue('qty', $qty);
        }
        $storeId = $this->storeManager->getStore()->getId();
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteLoader->load($cartId, $storeId);

        $product = $this->productLoader->load($data->getSku());

        try {
            $quote->addProduct($product, $qty);
            $quote->collectTotals()->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not add item to quote');
        }
        return true;
    }

    public function updateItem($cartId, $itemSku, \Magento\Checkout\Service\V1\Data\Cart\Item $data)
    {
        $qty = $data->getQty();
        if (!is_double($qty) || $qty <= 0) {
            throw InputException::invalidFieldValue('qty', $qty);
        }
        $storeId = $this->storeManager->getStore()->getId();
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteLoader->load($cartId, $storeId);
        $product = $this->productLoader->load($itemSku);
        $quoteItem = $quote->getItemByProduct($product);
        if (!$quoteItem) {
            throw new NoSuchEntityException("Cart $cartId doesn't contain product $itemSku");
        }
        $quoteItem->setData('qty', $data->getQty());

        try {
            $quote->collectTotals()->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not add item to quote');
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem($cartId, $itemSku)
    {
        $storeId = $this->storeManager->getStore()->getId();
        /** @var  \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteLoader->load($cartId, $storeId);
        $product = $this->productLoader->load($itemSku);
        $quoteItem = $quote->getItemByProduct($product);
        if (!$quoteItem) {
            throw new NoSuchEntityException("Cart $cartId doesn't contain product $itemSku");
        }
        try {
            $quote->removeItem($quoteItem->getId());
            $quote->collectTotals()->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not remove item from quote');
        }
        return true;
    }
}

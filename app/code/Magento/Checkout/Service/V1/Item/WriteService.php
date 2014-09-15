<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Item;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;

class WriteService implements WriteServiceInterface
{
    /**
     * @var \Magento\Checkout\Service\V1\QuoteLoader
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Catalog\Service\V1\Product\ProductLoader
     */
    protected $productLoader;

    /**
     * @param \Magento\Sales\Model\QuoteRepository $quoteRepository
     * @param \Magento\Catalog\Service\V1\Product\ProductLoader $productLoader
     */
    public function __construct(
        \Magento\Sales\Model\QuoteRepository $quoteRepository,
        \Magento\Catalog\Service\V1\Product\ProductLoader $productLoader
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->productLoader = $productLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function addItem($cartId, \Magento\Checkout\Service\V1\Data\Cart\Item $data)
    {
        $qty = $data->getQty();
        if (!is_numeric($qty) || $qty <= 0) {
            throw InputException::invalidFieldValue('qty', $qty);
        }
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId);

        $product = $this->productLoader->load($data->getSku());

        try {
            $quote->addProduct($product, $qty);
            $quote->collectTotals()->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not add item to quote');
        }
        return $quote->getItemByProduct($product)->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function updateItem($cartId, $itemId, \Magento\Checkout\Service\V1\Data\Cart\Item $data)
    {
        $qty = $data->getQty();
        if (!is_numeric($qty) || $qty <= 0) {
            throw InputException::invalidFieldValue('qty', $qty);
        }
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId);
        $quoteItem = $quote->getItemById($itemId);
        if (!$quoteItem) {
            throw new NoSuchEntityException("Cart $cartId doesn't contain item  $itemId");
        }
        $quoteItem->setData('qty', $qty);

        try {
            $quote->collectTotals()->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not update quote item');
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem($cartId, $itemId)
    {
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId);
        $quoteItem = $quote->getItemById($itemId);
        if (!$quoteItem) {
            throw new NoSuchEntityException("Cart $cartId doesn't contain item  $itemId");
        }
        try {
            $quote->removeItem($itemId);
            $quote->collectTotals()->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not remove item from quote');
        }
        return true;
    }
}

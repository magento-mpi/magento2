<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Item;

use Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Checkout\Service\V1\Data\ItemBuilder as ItemBuilder;
use \Magento\Checkout\Service\V1\Data\Item as Item;
use Magento\Framework\Exception\CouldNotSaveException;

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
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @param \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader
     * @param ItemBuilder $itemBuilder
     * @param \Magento\Catalog\Service\V1\Product\ProductLoader $productLoader
     */
    public function __construct(
        \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader,
        ItemBuilder $itemBuilder,
        \Magento\Catalog\Service\V1\Product\ProductLoader $productLoader,
        \Magento\Checkout\Model\Cart $cart
    ) {
        $this->quoteLoader = $quoteLoader;
        $this->itemBuilder = $itemBuilder;
        $this->productLoader = $productLoader;
        $this->cart = $cart;
    }

    /**
     * {@inheritdoc}
     */
    public function addItem($cartId, \Magento\Checkout\Service\V1\Data\Item $data)
    {
        /** @var  \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteLoader->load($cartId);

        $product = $this->productLoader->load($data->getSku());

        try {
            $quote->addProduct($product);
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not add item to quote');
        }
        return true;
    }
}
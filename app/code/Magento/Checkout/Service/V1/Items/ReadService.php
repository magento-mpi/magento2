<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Items;

use Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Checkout\Service\V1\Data\ItemsBuilder as ItemsBuilder;
use \Magento\Checkout\Service\V1\Data\Items as Items;

class ReadService implements ReadServiceInterface
{
    /**
     * @var \Magento\Checkout\Service\V1\QuoteLoader
     */
    protected $quoteLoader;

    /**
     * @var ItemsBuilder
     */
    protected $itemsBuilder;

    /**
     * @param \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader
     * @param ItemsBuilder $itemsBuilder
     */
    public function __construct(
        \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader,
        ItemsBuilder $itemsBuilder
    ) {
        $this->quoteLoader = $quoteLoader;
        $this->itemsBuilder = $itemsBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function itemsList($cartId)
    {
        $output = [];
        /** @var  \Magento\Sales\Model\Quote\Item $quote */
        $quote = $this->quoteLoader->load($cartId);

        /** @var  \Magento\Catalog\Model\Product $item */
        foreach ($quote->getAllItems() as $item) {
            $data = [
                Items::SKU => $item->getSku(),
                Items::NAME => $item->getName(),
                Items::PRICE => $item->getPrice(),
                Items::QTY => $item->getQty(),
                Items::TYPE => $item->getProductType()
            ];

            $output[] = $this->itemsBuilder->populateWithArray($data)->create();
        }
        return $output;
    }
}
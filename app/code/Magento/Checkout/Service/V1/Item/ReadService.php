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

class ReadService implements ReadServiceInterface
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
     * @param \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader
     * @param ItemBuilder $itemBuilder
     */
    public function __construct(
        \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader,
        ItemBuilder $itemBuilder
    ) {
        $this->quoteLoader = $quoteLoader;
        $this->itemBuilder = $itemBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($cartId)
    {
        $output = [];
        /** @var  \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteLoader->load($cartId);

        /** @var  \Magento\Sales\Model\Quote\Item  $item */
        foreach ($quote->getAllItems() as $item) {
            $data = [
                Item::SKU => $item->getSku(),
                Item::NAME => $item->getName(),
                Item::PRICE => $item->getPrice(),
                Item::QTY => $item->getQty(),
                Item::TYPE => $item->getProductType()
            ];

            $output[] = $this->itemBuilder->populateWithArray($data)->create();
        }
        return $output;
    }
}
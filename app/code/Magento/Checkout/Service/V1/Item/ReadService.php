<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Item;

class ReadService implements ReadServiceInterface
{
    /**
     * @var \Magento\Checkout\Service\V1\QuoteLoader
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Checkout\Service\V1\Data\Cart\ItemMapper
     */
    protected $itemMapper;

    /**
     * @param \Magento\Sales\Model\QuoteRepository $quoteRepository
     * @param \Magento\Checkout\Service\V1\Data\Cart\ItemMapper $itemMapper
     */
    public function __construct(
        \Magento\Sales\Model\QuoteRepository $quoteRepository,
        \Magento\Checkout\Service\V1\Data\Cart\ItemMapper $itemMapper
    ) {
         $this->quoteRepository = $quoteRepository;
         $this->itemMapper = $itemMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($cartId)
    {
        $output = [];
        /** @var  \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId);

        /** @var  \Magento\Sales\Model\Quote\Item  $item */
        foreach ($quote->getAllItems() as $item) {

            $output[] = $this->itemMapper->extractDto($item);
        }
        return $output;
    }
}

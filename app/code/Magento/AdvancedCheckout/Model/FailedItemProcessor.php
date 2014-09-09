<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Model;

class FailedItemProcessor
{
    /**
     * @var \Magento\Sales\Model\Quote
     */
    protected $_quote;

    /**
     * @var \Magento\Sales\Model\QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * @var \Magento\Sales\Model\Quote\AddressFactory
     */
    protected $_addressFactory;

    /**
     * @var \Magento\Framework\Data\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Checkout data
     *
     * @var \Magento\AdvancedCheckout\Helper\Data
     */
    protected $_checkoutData;

    /**
     * @param \Magento\Sales\Model\Quote $quote
     * @param Cart $cart
     * @param \Magento\Framework\Data\CollectionFactory $collectionFactory
     * @param \Magento\AdvancedCheckout\Helper\Data $checkoutData
     * @param \Magento\Sales\Model\QuoteFactory $quoteFactory
     * @param \Magento\Sales\Model\Quote\AddressFactory $addressFactory
     */
    public function __construct(
        \Magento\Sales\Model\Quote $quote,
        \Magento\Framework\Data\CollectionFactory $collectionFactory,
        \Magento\AdvancedCheckout\Helper\Data $checkoutData,
        \Magento\Sales\Model\QuoteFactory $quoteFactory,
        \Magento\Sales\Model\Quote\AddressFactory $addressFactory
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_quote = $quote;
        $this->_checkoutData = $checkoutData;
        $this->_quoteFactory = $quoteFactory;
        $this->_addressFactory = $addressFactory;
    }

    /**
     * Copy real address to the quote
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @param \Magento\Sales\Model\Quote\Address $realAddress
     * @return \Magento\Sales\Model\Quote\Address
     */
    protected function _copyAddress($quote, $realAddress)
    {
        $address = $this->_addressFactory->create();
        $address->setData($realAddress->getData());
        $address->setId(
            null
        )->unsEntityId()->unsetData(
            'cached_items_nominal'
        )->unsetData(
            'cached_items_nonnominal'
        )->unsetData(
            'cached_items_all'
        )->setQuote(
            $quote
        );
        return $address;
    }

    /**
     * Process failed items
     * @return void
     */
    public function process()
    {
        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = $this->_quoteFactory->create();
        $collection = $this->_collectionFactory->create();

        foreach ($this->_checkoutData->getFailedItems(false) as $item) {
            /** @var $item \Magento\Sales\Model\Quote\Item */
            if ((double)$item->getQty() <= 0) {
                $item->setSkuRequestedQty($item->getQty());
                $item->setData('qty', 1);
            }
            $item->setQuote($quote);
            $collection->addItem($item);
        }

        $quote->preventSaving()->setItemsCollection($collection);

        $quote->setShippingAddress($this->_copyAddress($quote, $this->_quote->getShippingAddress()));
        $quote->setBillingAddress($this->_copyAddress($quote, $this->_quote->getBillingAddress()));
        $quote->setTotalsCollectedFlag(false)->collectTotals();

        foreach ($quote->getAllItems() as $item) {
            /** @var $item \Magento\Sales\Model\Quote\Item */
            if ($item->hasSkuRequestedQty()) {
                $item->setData('qty', $item->getSkuRequestedQty());
            }
        }
    }
}

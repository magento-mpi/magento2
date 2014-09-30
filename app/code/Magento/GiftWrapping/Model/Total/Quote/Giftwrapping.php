<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * GiftWrapping total calculator for quote
 *
 */
namespace Magento\GiftWrapping\Model\Total\Quote;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Giftwrapping extends \Magento\Sales\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @var \Magento\Store\Model\Store
     */
    protected $_store;

    /**
     * @var \Magento\Sales\Model\Quote
     */
    protected $_quote;

    /**
     * @var \Magento\Sales\Model\Quote|\Magento\Sales\Model\Quote\Address
     */
    protected $_quoteEntity;

    /**
     * Gift wrapping data
     *
     * @var \Magento\GiftWrapping\Helper\Data
     */
    protected $_giftWrappingData = null;

    /**
     * @var \Magento\GiftWrapping\Model\WrappingFactory
     */
    protected $_wrappingFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param \Magento\GiftWrapping\Helper\Data $giftWrappingData
     * @param \Magento\GiftWrapping\Model\WrappingFactory $wrappingFactory
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\GiftWrapping\Helper\Data $giftWrappingData,
        \Magento\GiftWrapping\Model\WrappingFactory $wrappingFactory,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->_giftWrappingData = $giftWrappingData;
        $this->_wrappingFactory = $wrappingFactory;
        $this->setCode('giftwrapping');
    }

    /**
     * Collect gift wrapping totals
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  \Magento\GiftWrapping\Model\Total\Quote\Giftwrapping
     */
    public function collect(\Magento\Sales\Model\Quote\Address $address)
    {
        parent::collect($address);
        if ($address->getAddressType() != \Magento\Sales\Model\Quote\Address::TYPE_SHIPPING) {
            return $this;
        }

        $this->_quote = $address->getQuote();
        $this->_store = $this->_quote->getStore();
        $quote = $this->_quote;
        if ($quote->getIsMultiShipping()) {
            $this->_quoteEntity = $address;
        } else {
            $this->_quoteEntity = $quote;
        }

        $this->_collectWrappingForItems($address)->_collectWrappingForQuote($address)->_collectPrintedCard($address);

        $address->setBaseGrandTotal(
            $address->getBaseGrandTotal() +
            $address->getGwItemsBasePrice() +
            $address->getGwBasePrice() +
            $address->getGwCardBasePrice()
        );
        $address->setGrandTotal(
            $address->getGrandTotal() +
            $address->getGwItemsPrice() +
            $address->getGwPrice() +
            $address->getGwCardPrice()
        );

        if ($quote->getIsNewGiftWrappingCollecting()) {
            $quote->setGwItemsBasePrice(0);
            $quote->setGwItemsPrice(0);
            $quote->setGwBasePrice(0);
            $quote->setGwPrice(0);
            $quote->setGwCardBasePrice(0);
            $quote->setGwCardPrice(0);
            $quote->setIsNewGiftWrappingCollecting(false);
        }
        $quote->setGwItemsBasePrice($address->getGwItemsBasePrice() + $quote->getGwItemsBasePrice());
        $quote->setGwItemsPrice($address->getGwItemsPrice() + $quote->getGwItemsPrice());
        $quote->setGwBasePrice($address->getGwBasePrice() + $quote->getGwBasePrice());
        $quote->setGwPrice($address->getGwPrice() + $quote->getGwPrice());
        $quote->setGwCardBasePrice($address->getGwCardBasePrice() + $quote->getGwCardBasePrice());
        $quote->setGwCardPrice($address->getGwCardPrice() + $quote->getGwCardPrice());

        return $this;
    }

    /**
     * Collect wrapping total for items
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  \Magento\GiftWrapping\Model\Total\Quote\Giftwrapping
     */
    protected function _collectWrappingForItems($address)
    {
        $items = $this->_getAddressItems($address);
        $wrappingForItemsBaseTotal = false;
        $wrappingForItemsTotal = false;

        foreach ($items as $item) {
            if ($item->getProduct()->isVirtual() || $item->getParentItem() || !$item->getGwId()) {
                continue;
            }
            if ($item->getProduct()->getGiftWrappingPrice()) {
                $wrappingBasePrice = $item->getProduct()->getGiftWrappingPrice();
            } else {
                $wrapping = $this->_getWrapping($item->getGwId(), $this->_store);
                $wrappingBasePrice = $wrapping->getBasePrice();
            }
            $wrappingPrice = $this->priceCurrency->convert($wrappingBasePrice, $this->_store);
            $item->setGwBasePrice($wrappingBasePrice);
            $item->setGwPrice($wrappingPrice);
            $wrappingForItemsBaseTotal += $wrappingBasePrice * $item->getQty();
            $wrappingForItemsTotal += $wrappingPrice * $item->getQty();
        }
        $address->setGwItemsBasePrice($wrappingForItemsBaseTotal);
        $address->setGwItemsPrice($wrappingForItemsTotal);

        return $this;
    }

    /**
     * Collect wrapping total for quote
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  \Magento\GiftWrapping\Model\Total\Quote\Giftwrapping
     */
    protected function _collectWrappingForQuote($address)
    {
        $wrappingBasePrice = false;
        $wrappingPrice = false;
        if ($this->_quoteEntity->getGwId()) {
            $wrapping = $this->_getWrapping($this->_quoteEntity->getGwId(), $this->_store);
            $wrappingBasePrice = $wrapping->getBasePrice();
            $wrappingPrice = $this->priceCurrency->convert($wrappingBasePrice, $this->_store);
        }
        $address->setGwBasePrice($wrappingBasePrice);
        $address->setGwPrice($wrappingPrice);
        return $this;
    }

    /**
     * Collect printed card total for quote
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  \Magento\GiftWrapping\Model\Total\Quote\Giftwrapping
     */
    protected function _collectPrintedCard($address)
    {
        $printedCardBasePrice = false;
        $printedCardPrice = false;
        if ($this->_quoteEntity->getGwAddCard()) {
            $printedCardBasePrice = $this->_giftWrappingData->getPrintedCardPrice($this->_store);
            $printedCardPrice = $this->priceCurrency->convert($printedCardBasePrice, $this->_store);
        }
        $address->setGwCardBasePrice($printedCardBasePrice);
        $address->setGwCardPrice($printedCardPrice);
        return $this;
    }

    /**
     * Return wrapping model for wrapping ID
     *
     * @param  int $wrappingId
     * @param  \Magento\Store\Model\Store $store
     * @return \Magento\GiftWrapping\Model\Wrapping
     */
    protected function _getWrapping($wrappingId, $store)
    {
        /** @var \Magento\GiftWrapping\Model\Wrapping $wrapping */
        $wrapping = $this->_wrappingFactory->create();
        $wrapping->setStoreId($store->getId());
        $wrapping->load($wrappingId);
        return $wrapping;
    }

    /**
     * Assign wrapping totals and labels to address object
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  \Magento\Sales\Model\Quote\Address\Total\Subtotal
     */
    public function fetch(\Magento\Sales\Model\Quote\Address $address)
    {
        $address->addTotal(
            array(
                'code' => $this->getCode(),
                'gw_price' => $address->getGwPrice(),
                'gw_base_price' => $address->getGwBasePrice(),
                'gw_items_price' => $address->getGwItemsPrice(),
                'gw_items_base_price' => $address->getGwItemsBasePrice(),
                'gw_card_price' => $address->getGwCardPrice(),
                'gw_card_base_price' => $address->getGwCardBasePrice()
            )
        );
        return $this;
    }
}

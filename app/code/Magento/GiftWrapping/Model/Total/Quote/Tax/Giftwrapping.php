<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Model\Total\Quote\Tax;

use Magento\Sales\Model\Quote\Address\Total\AbstractTotal;
use Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;

/**
 * GiftWrapping tax total calculator for quote
 */
class Giftwrapping extends CommonTaxCollector
{
    /**
     * Constant for item gift wrapping item type
     */
    const ITEM_TYPE = 'item_gw';

    /**
     * Constant for quote gift wrapping item type
     */
    const QUOTE_TYPE = 'quote_gw';

    /**
     * Constant for item print card item type
     */
    const PRINTED_CARD_TYPE = 'printed_card_gw';

    /**
     * Constant for item gift wrapping code prefix
     */
    const CODE_ITEM_GW_PREFIX = 'item_gw';

    /**
     * Constant for quote gift wrapping code
     */
    const CODE_QUOTE_GW = 'quote_gw';

    /**
     * Constant for printed card code
     */
    const CODE_PRINTED_CARD = 'printed_card_gw';

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
     * @param \Magento\GiftWrapping\Helper\Data $giftWrappingData
     */
    public function __construct(
        \Magento\GiftWrapping\Helper\Data $giftWrappingData
    ) {
        $this->_giftWrappingData = $giftWrappingData;
        $this->setCode('tax_giftwrapping');
    }

    /**
     * Collect gift wrapping related items and add them to tax calculation
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  $this
     */
    public function collect(\Magento\Sales\Model\Quote\Address $address)
    {
        parent::collect($address);
        if ($address->getAddressType() != \Magento\Sales\Model\Quote\Address::TYPE_SHIPPING) {
            return $this;
        }

        $this->_quote = $address->getQuote();
        $quote = $this->_quote;
        if ($quote->getIsMultiShipping()) {
            $this->_quoteEntity = $address;
        } else {
            $this->_quoteEntity = $quote;
        }

        $store = $address->getQuote()->getStore();
        $productTaxClassId = $this->_giftWrappingData->getWrappingTaxClass($store);

        $this->_collectWrappingForItems($address, $productTaxClassId);
        $this->_collectWrappingForQuote($address, $productTaxClassId);
        $this->_collectPrintedCard($address, $productTaxClassId);

        return $this;
    }

    /**
     * Collect wrapping tax total for items
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @param   int $gwTaxClassId
     * @return  $this
     */
    protected function _collectWrappingForItems($address, $gwTaxClassId)
    {
        $items = $this->_getAddressItems($address);
        $gwItemCodeToItemMapping = [];

        foreach ($items as $item) {
            if ($item->getProduct()->isVirtual() || $item->getParentItem() || !$item->getGwId()) {
                continue;
            }

            $associatedTaxables = $item->getAssociatedTaxables();
            if (!$associatedTaxables) {
                $associatedTaxables = [];
            }

            $gwBasePrice = $item->getGwBasePrice();
            $gwPrice = $item->getGwPrice();
            $gwItemCode = self::CODE_ITEM_GW_PREFIX . $this->getNextIncrement();

            $gwItemCodeToItemMapping[$gwItemCode] = $item;

            $associatedTaxables[] = [
                self::KEY_ASSOCIATED_TAXABLE_TYPE => self::ITEM_TYPE,
                self::KEY_ASSOCIATED_TAXABLE_CODE => $gwItemCode,
                self::KEY_ASSOCIATED_TAXABLE_UNIT_PRICE => $gwPrice,
                self::KEY_ASSOCIATED_TAXABLE_BASE_UNIT_PRICE => $gwBasePrice,
                self::KEY_ASSOCIATED_TAXABLE_QUANTITY => $item->getQty(),
                self::KEY_ASSOCIATED_TAXABLE_TAX_CLASS_ID => $gwTaxClassId,
                self::KEY_ASSOCIATED_TAXABLE_PRICE_INCLUDES_TAX => false,
            ];

            $item->setAssociatedTaxables($associatedTaxables);
        }

        $address->setGWItemCodeToItemMapping($gwItemCodeToItemMapping);

        return $this;
    }

    /**
     * Collect wrapping tax total for quote
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  $this
     */
    protected function _collectWrappingForQuote($address, $gwTaxClassId)
    {
        if ($this->_quoteEntity->getGwId()) {
            $associatedTaxables = $address->getAssociatedTaxables();
            if (!$associatedTaxables) {
                $associatedTaxables = [];
            }

            $wrappingBaseAmount = $this->_quoteEntity->getGwBasePrice();
            $wrappingAmount = $this->_quoteEntity->getGwPrice();

            $associatedTaxables[] = [
                self::KEY_ASSOCIATED_TAXABLE_TYPE => self::QUOTE_TYPE,
                self::KEY_ASSOCIATED_TAXABLE_CODE => self::CODE_QUOTE_GW,
                self::KEY_ASSOCIATED_TAXABLE_UNIT_PRICE => $wrappingAmount,
                self::KEY_ASSOCIATED_TAXABLE_BASE_UNIT_PRICE => $wrappingBaseAmount,
                self::KEY_ASSOCIATED_TAXABLE_QUANTITY => 1,
                self::KEY_ASSOCIATED_TAXABLE_TAX_CLASS_ID => $gwTaxClassId,
                self::KEY_ASSOCIATED_TAXABLE_PRICE_INCLUDES_TAX => false,
                self::KEY_ASSOCIATED_TAXABLE_ASSOCIATION_ITEM_CODE => self::ASSOCIATION_ITEM_CODE_FOR_QUOTE,
            ];

            $address->setAssociatedTaxables($associatedTaxables);
        }
        return $this;
    }

    /**
     * Collect printed card tax total for quote
     *
     * @param \Magento\Sales\Model\Quote\Address $address
     * @param int $gwTaxClassId
     * @return $this
     */
    protected function _collectPrintedCard($address, $gwTaxClassId)
    {
        if ($this->_quoteEntity->getGwAddCard()) {
            $associatedTaxables = $address->getAssociatedTaxables();
            if (!$associatedTaxables) {
                $associatedTaxables = [];
            }

            $printedCardBaseTaxAmount = $this->_quoteEntity->getGwCardBasePrice();
            $printedCardTaxAmount = $this->_quoteEntity->getGwCardPrice();

            $associatedTaxables[] = [
                self::KEY_ASSOCIATED_TAXABLE_TYPE => self::PRINTED_CARD_TYPE,
                self::KEY_ASSOCIATED_TAXABLE_CODE => self::CODE_PRINTED_CARD,
                self::KEY_ASSOCIATED_TAXABLE_UNIT_PRICE => $printedCardTaxAmount,
                self::KEY_ASSOCIATED_TAXABLE_BASE_UNIT_PRICE => $printedCardBaseTaxAmount,
                self::KEY_ASSOCIATED_TAXABLE_QUANTITY => 1,
                self::KEY_ASSOCIATED_TAXABLE_TAX_CLASS_ID => $gwTaxClassId,
                self::KEY_ASSOCIATED_TAXABLE_PRICE_INCLUDES_TAX => false,
                self::KEY_ASSOCIATED_TAXABLE_ASSOCIATION_ITEM_CODE => self::ASSOCIATION_ITEM_CODE_FOR_QUOTE,
            ];

            $address->setAssociatedTaxables($associatedTaxables);
        }
        return $this;
    }

    /**
     * Assign wrapping tax totals and labels to address object
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  $this
     */
    public function fetch(\Magento\Sales\Model\Quote\Address $address)
    {
        return $this;
    }
}

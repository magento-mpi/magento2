<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Model\Total\Quote\Tax;

use Magento\Sales\Model\Quote\Address\Total\AbstractTotal;
use \Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;
use Magento\Tax\Service\V1\Data\TaxDetails\Item as ItemTaxDetails;

/**
 * GiftWrapping tax total calculator for quote
 */
class GiftwrappingAfterTax extends Giftwrapping
{
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
     * Collect gift wrapping tax totals
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  $this
     */
    public function collect(\Magento\Sales\Model\Quote\Address $address)
    {
        AbstractTotal::collect($address);
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

        $extraTaxableDetails = $address->getExtraTaxableDetails();
        if (isset($extraTaxableDetails[self::ITEM_TYPE])) {
            $this->processWrappingForItems($address, $extraTaxableDetails[self::ITEM_TYPE]);
        }

        if (isset($extraTaxableDetails[self::QUOTE_TYPE])) {
            $this->processWrappingForQuote($address, $extraTaxableDetails[self::QUOTE_TYPE]);
        }

        if (isset($extraTaxableDetails[self::PRINTED_CARD_TYPE])) {
            $this->processPrintedCard($address, $extraTaxableDetails[self::PRINTED_CARD_TYPE]);
        }

        if ($quote->getIsNewGiftWrappingTaxCollecting()) {
            $quote->setGwItemsBaseTaxAmount(0);
            $quote->setGwItemsTaxAmount(0);
            $quote->setGwBaseTaxAmount(0);
            $quote->setGwTaxAmount(0);
            $quote->setGwCardBaseTaxAmount(0);
            $quote->setGwCardTaxAmount(0);
            $quote->setIsNewGiftWrappingTaxCollecting(false);
        }
        $quote->setGwItemsBaseTaxAmount($address->getGwItemsBaseTaxAmount() + $quote->getGwItemsBaseTaxAmount());
        $quote->setGwItemsTaxAmount($address->getGwItemsTaxAmount() + $quote->getGwItemsTaxAmount());
        $quote->setGwBaseTaxAmount($address->getGwBaseTaxAmount() + $quote->getGwBaseTaxAmount());
        $quote->setGwTaxAmount($address->getGwTaxAmount() + $quote->getGwTaxAmount());
        $quote->setGwCardBaseTaxAmount($address->getGwCardBaseTaxAmount() + $quote->getGwCardBaseTaxAmount());
        $quote->setGwCardTaxAmount($address->getGwCardTaxAmount() + $quote->getGwCardTaxAmount());

        return $this;
    }

    /**
     * Update wrapping tax total for items
     *
     * @param  \Magento\Sales\Model\Quote\Address $address
     * @param  array $itemTaxDetails
     * @return  $this
     */
    protected function processWrappingForItems($address, $itemTaxDetails)
    {
        $gwItemCodeToItemMapping = $address->getGWItemCodeToItemMapping();
        $wrappingForItemsBaseTaxAmount = false;
        $wrappingForItemsTaxAmount = false;

        foreach ($itemTaxDetails as $itemCode => $itemTaxDetail) {
            // order may have multiple giftwrapping items
            for ($i = 0; $i < count($itemTaxDetail); $i++) {
                $gwTaxDetail = $itemTaxDetail[$i];
                $gwItemCode = $gwTaxDetail['code'];

                if (!array_key_exists($gwItemCode, $gwItemCodeToItemMapping)) {
                    continue;
                }
                $item = $gwItemCodeToItemMapping[$gwItemCode];

                // search for the right giftwrapping item associated with the address
                if ($item != null) {
                    break;
                }
            }

            $wrappingBaseTaxAmount = $gwTaxDetail['base_row_tax'];
            $wrappingTaxAmount = $gwTaxDetail['row_tax'];
            $item->setGwBaseTaxAmount($wrappingBaseTaxAmount / $item->getQty());
            $item->setGwTaxAmount($wrappingTaxAmount / $item->getQty());

            $wrappingForItemsBaseTaxAmount += $wrappingBaseTaxAmount;
            $wrappingForItemsTaxAmount += $wrappingTaxAmount;
        }

        $address->setGwItemsBaseTaxAmount($wrappingForItemsBaseTaxAmount);
        $address->setGwItemsTaxAmount($wrappingForItemsTaxAmount);
        return $this;
    }

    /**
     * Collect wrapping tax total for quote
     *
     * @param \Magento\Sales\Model\Quote\Address $address
     * @param array $itemTaxDetails
     * @return $this
     */
    protected function processWrappingForQuote($address, $itemTaxDetails)
    {
        //there is only one gift wrapping per quote
        $gwTaxDetail = $itemTaxDetails[CommonTaxCollector::ASSOCIATION_ITEM_CODE_FOR_QUOTE][0];

        $wrappingBaseTaxAmount = $gwTaxDetail['base_row_tax'];
        $wrappingTaxAmount = $gwTaxDetail['row_tax'];

        $address->setGwBaseTaxAmount($wrappingBaseTaxAmount);
        $address->setGwTaxAmount($wrappingTaxAmount);
        return $this;
    }

    /**
     * Collect printed card tax total for quote
     *
     * @param \Magento\Sales\Model\Quote\Address $address
     * @param array $itemTaxDetails
     * @return $this
     */
    protected function processPrintedCard($address, $itemTaxDetails)
    {
        //there is only one printed card per quote
        $taxDetail = $itemTaxDetails[CommonTaxCollector::ASSOCIATION_ITEM_CODE_FOR_QUOTE][0];

        $printedCardBaseTaxAmount = $taxDetail['base_row_tax'];
        $printedCardTaxAmount = $taxDetail['row_tax'];

        $address->setGwCardBaseTaxAmount($printedCardBaseTaxAmount);
        $address->setGwCardTaxAmount($printedCardTaxAmount);
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
        $address->addTotal(
            array(
                'code' => 'giftwrapping',
                'gw_price' => $address->getGwPrice(),
                'gw_base_price' => $address->getGwBasePrice(),
                'gw_items_price' => $address->getGwItemsPrice(),
                'gw_items_base_price' => $address->getGwItemsBasePrice(),
                'gw_card_price' => $address->getGwCardPrice(),
                'gw_card_base_price' => $address->getGwCardBasePrice(),
                'gw_tax_amount' => $address->getGwTaxAmount(),
                'gw_base_tax_amount' => $address->getGwBaseTaxAmount(),
                'gw_items_tax_amount' => $address->getGwItemsTaxAmount(),
                'gw_items_base_tax_amount' => $address->getGwItemsBaseTaxAmount(),
                'gw_card_tax_amount' => $address->getGwCardTaxAmount(),
                'gw_card_base_tax_amount' => $address->getGwCardBaseTaxAmount()
            )
        );
        return $this;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * GiftWrapping tax total calculator for quote
 */
namespace Magento\GiftWrapping\Model\Total\Quote\Tax;

class Giftwrapping extends \Magento\Sales\Model\Quote\Address\Total\AbstractTotal
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
     * @var \Magento\Tax\Model\Calculation
     */
    protected $_taxCalculationModel;

    /**
     * @var array
     */
    protected $_request;

    /**
     * @var float
     */
    protected $_rate;

    /**
     * Gift wrapping data
     *
     * @var \Magento\GiftWrapping\Helper\Data
     */
    protected $_giftWrappingData = null;

    /**
     * @param \Magento\GiftWrapping\Helper\Data $giftWrappingData
     * @param \Magento\Tax\Model\Calculation $taxCalculation
     */
    public function __construct(
        \Magento\GiftWrapping\Helper\Data $giftWrappingData,
        \Magento\Tax\Model\Calculation $taxCalculation
    ) {
        $this->_giftWrappingData = $giftWrappingData;
        $this->_taxCalculationModel = $taxCalculation;
        $this->setCode('tax_giftwrapping');
    }

    /**
     * Collect applied tax rates information on address level
     *
     * @param \Magento\Sales\Model\Quote\Address $address
     * @param array $applied
     * @param float $amount
     * @param float $baseAmount
     * @param float $rate
     * @return \Magento\GiftWrapping\Model\Total\Quote\Tax\Giftwrapping
     */
    protected function _saveAppliedTaxes($address, $applied, $amount, $baseAmount, $rate)
    {
        $previouslyAppliedTaxes = $address->getAppliedTaxes();
        $process = count($previouslyAppliedTaxes);

        foreach ($applied as $row) {
            if ($row['percent'] == 0) {
                continue;
            }
            if (!isset($previouslyAppliedTaxes[$row['id']])) {
                $row['process']     = $process;
                $row['amount']      = 0;
                $row['base_amount'] = 0;
                $previouslyAppliedTaxes[$row['id']] = $row;
            }

            if (!is_null($row['percent'])) {
                $row['percent'] = $row['percent'] ? $row['percent'] : 1;
                $rate = $rate ? $rate : 1;

                $appliedAmount     = $amount/$rate * $row['percent'];
                $baseAppliedAmount = $baseAmount/$rate * $row['percent'];
            } else {
                $appliedAmount     = 0;
                $baseAppliedAmount = 0;
                foreach ($row['rates'] as $rate) {
                    $appliedAmount      += $rate['amount'];
                    $baseAppliedAmount  += $rate['base_amount'];
                }
            }

            if ($appliedAmount || $previouslyAppliedTaxes[$row['id']]['amount']) {
                $previouslyAppliedTaxes[$row['id']]['amount']      += $appliedAmount;
                $previouslyAppliedTaxes[$row['id']]['base_amount'] += $baseAppliedAmount;
            } else {
                unset($previouslyAppliedTaxes[$row['id']]);
            }
        }
        $address->setAppliedTaxes($previouslyAppliedTaxes);
        return $this;
    }

    /**
     * Collect gift wrapping tax totals
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  \Magento\GiftWrapping\Model\Total\Quote\Tax\Giftwrapping
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

        $this->_initRate($address)
            ->_collectWrappingForItems($address)
            ->_collectWrappingForQuote($address)
            ->_collectPrintedCard($address);

        $baseTaxAmount = $address->getGwItemsBaseTaxAmount()
            + $address->getGwBaseTaxAmount()
            + $address->getGwCardBaseTaxAmount();
        $taxAmount = $address->getGwItemsTaxAmount()
            + $address->getGwTaxAmount()
            + $address->getGwCardTaxAmount();
        $address->setBaseTaxAmount($address->getBaseTaxAmount() + $baseTaxAmount);
        $address->setTaxAmount($address->getTaxAmount() + $taxAmount);
        $address->setBaseGrandTotal($address->getBaseGrandTotal() + $baseTaxAmount);
        $address->setGrandTotal($address->getGrandTotal() + $taxAmount);

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
        $quote->setGwCardBaseTaxAmount(
            $address->getGwCardBaseTaxAmount() + $quote->getGwCardBaseTaxAmount()
        );
        $quote->setGwCardTaxAmount(
            $address->getGwCardTaxAmount() + $quote->getGwCardTaxAmount()
        );

        $applied = $this->_taxCalculationModel->getAppliedRates($this->_request);
        $this->_saveAppliedTaxes($address, $applied, $taxAmount, $baseTaxAmount, $this->_rate);

        return $this;
    }

    /**
     * Collect wrapping tax total for items
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  \Magento\GiftWrapping\Model\Total\Quote\Tax\Giftwrapping
     */
    protected function _collectWrappingForItems($address)
    {
        $items = $this->_getAddressItems($address);
        $wrappingForItemsBaseTaxAmount = false;
        $wrappingForItemsTaxAmount = false;

        foreach ($items as $item) {
            if ($item->getProduct()->isVirtual() || $item->getParentItem() || !$item->getGwId()) {
                continue;
            }
            $wrappingBaseTaxAmount = $this->_calcTaxAmount($item->getGwBasePrice());
            $wrappingTaxAmount = $this->_calcTaxAmount($item->getGwPrice());
            $item->setGwBaseTaxAmount($wrappingBaseTaxAmount);
            $item->setGwTaxAmount($wrappingTaxAmount);

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
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  \Magento\GiftWrapping\Model\Total\Quote\Tax\Giftwrapping
     */
    protected function _collectWrappingForQuote($address)
    {
        $wrappingBaseTaxAmount = false;
        $wrappingTaxAmount = false;
        if ($this->_quoteEntity->getGwId()) {
            $wrappingBaseTaxAmount = $this->_calcTaxAmount($this->_quoteEntity->getGwBasePrice());
            $wrappingTaxAmount = $this->_calcTaxAmount($this->_quoteEntity->getGwPrice());
        }
        $address->setGwBaseTaxAmount($wrappingBaseTaxAmount);
        $address->setGwTaxAmount($wrappingTaxAmount);
        return $this;
    }

    /**
     * Collect printed card tax total for quote
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  \Magento\GiftWrapping\Model\Total\Quote\Tax\Giftwrapping
     */
    protected function _collectPrintedCard($address)
    {
        $printedCardBaseTaxAmount = false;
        $printedCardTaxAmount = false;
        if ($this->_quoteEntity->getGwAddCard()) {
            $printedCardBaseTaxAmount = $this->_calcTaxAmount($this->_quoteEntity->getGwCardBasePrice());
            $printedCardTaxAmount = $this->_calcTaxAmount($this->_quoteEntity->getGwCardPrice());
        }
        $address->setGwCardBaseTaxAmount($printedCardBaseTaxAmount);
        $address->setGwCardTaxAmount($printedCardTaxAmount);
        return $this;
    }

    /**
     * Init gift wrapping and printed card tax rate for address
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  \Magento\GiftWrapping\Model\Total\Quote\Tax\Giftwrapping
     */
    protected function _initRate($address)
    {
        $store = $address->getQuote()->getStore();
        $billingAddress = $address->getQuote()->getBillingAddress();
        $custTaxClassId = $address->getQuote()->getCustomerTaxClassId();
        $this->_request = $this->_taxCalculationModel->getRateRequest(
            $address,
            $billingAddress,
            $custTaxClassId,
            $store
        );
        $this->_request->setProductClassId($this->_giftWrappingData->getWrappingTaxClass($store));
        $this->_rate = $this->_taxCalculationModel->getRate($this->_request);
        return $this;
    }

    /**
     * Calculate tax for amount
     *
     * @param   float $price
     * @return  float
     */
    protected function _calcTaxAmount($price)
    {
        return $this->_taxCalculationModel->calcTaxAmount($price, $this->_rate);
    }

    /**
     * Assign wrapping tax totals and labels to address object
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  \Magento\GiftWrapping\Model\Total\Quote\Tax\Giftwrapping
     */
    public function fetch(\Magento\Sales\Model\Quote\Address $address)
    {
        $address->addTotal(array(
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
        ));
        return $this;
    }
}

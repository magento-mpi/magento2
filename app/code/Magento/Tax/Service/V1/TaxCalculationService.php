<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Tax\Model\Calculation;
use Magento\Tax\Service\V1\Data\QuoteDetails;
use Magento\Tax\Service\V1\Data\QuoteDetails\Item as QuoteDetailsItem;
use Magento\Tax\Service\V1\Data\TaxDetails;
use Magento\Tax\Service\V1\Data\TaxDetails\Item as TaxDetailsItem;
use Magento\Tax\Service\V1\Data\TaxDetails\ItemBuilder as TaxDetailsItemBuilder;
use Magento\Tax\Service\V1\Data\TaxDetailsBuilder;

/**
 * Tax Calculation Service
 *
 */
class TaxCalculationService implements TaxCalculationServiceInterface
{
    /**
     * Tax calculation model
     *
     * @var Calculation
     */
    protected $calculator;

    /**
     * Tax configuration object
     *
     * @var \Magento\Tax\Model\Config
     */
    protected $config;

    /**
     * Tax Details builder
     *
     * @var TaxDetailsBuilder
     */
    protected $taxDetailsBuilder;

    /**
     * Tax helper
     *
     * @var \Magento\Tax\Helper\Data
     */
    protected $helper;

    /**
     * Rounding deltas for prices
     *
     * @var array
     * example:
     *  [
     *      'type' => [
     *          'rate' => 'rounding delta',
     *      ],
     *  ]
     */
    protected $roundingDeltas;

    /**
     * Tax details item builder
     *
     * @var TaxDetailsBuilderItem
     */
    protected $taxDetailsItemBuilder;

    /**
     * Constructor
     *
     * @param Calculation $calculation
     * @param \Magento\Tax\Model\Config $config
     * @param \Magento\Tax\Helper\Data $helper
     * @param TaxDetailsBuilder $taxDetailsBuilder
     * @param TaxDetailsItemBuilder $taxDetailsItemBuilder
     */
    public function __construct(
        Calculation $calculation,
        \Magento\Tax\Model\Config $config,
        \Magento\Tax\Helper\Data $helper,
        TaxDetailsBuilder $taxDetailsBuilder,
        TaxDetailsItemBuilder $taxDetailsItemBuilder
    ) {
        $this->calculator = $calculation;
        $this->config = $config;
        $this->helper = $helper;
        $this->taxDetailsBuilder = $taxDetailsBuilder;
        $this->taxDetailsItemBuilder = $taxDetailsItemBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateTax(QuoteDetails $quoteDetails, $storeId)
    {
        // init taxDetailsBuilder
        $taxDetails = $this->taxDetailsBuilder->setDiscountAmount(0)
            ->setSubtotal(0)
            ->setTaxableAmount(0)
            ->setTaxAmount(0)
            ->create();

        $items = $quoteDetails->getItems();
        if (empty($items)) {
            return $taxDetails;
        }
        $this->calculator->setCustomerData($quoteDetails->getCustomer());

        $addressRequest = $this->getAddressTaxRequest($quoteDetails, $storeId);
        if ($this->config->priceIncludesTax($storeId)) {
            $storeRequest = $this->getStoreTaxRequest($storeId);
            $classIds = [];
            foreach ($items as $item) {
                if ($item->getTaxClassId()) {
                    $classIds[] = $item->getTaxClassId();
                }
            }
            $classIds = array_unique($classIds);
            $addressRequest->setProductClassId($classIds);
            $storeRequest->setProductClassId($classIds);
            if ($this->helper->isCrossBorderTradeEnabled($storeId)) {
                $addressRequest->setSameRateAsStore(true);
            } else {
                $addressRequest->setSameRateAsStore(
                    $this->calculator->compareRequests($storeRequest, $addressRequest)
                );
            }
        }
        if (!$addressRequest->getSameRateAsStore()) {
            // Check current request individually
            $rate = $this->calculator->getRate($addressRequest);
            $storeRate = $this->calculator->getStoreRate($addressRequest, $storeId);
            $addressRequest->setSameRateAsStore($rate == $storeRate);
        }

        // init rounding deltas for this quote
        $this->roundingDeltas = [];
        foreach ($items as $item) {
            $taxDetailsItem = $this->processItem($item, $addressRequest, $storeId);
            if (null != $taxDetailsItem) {
                $taxDetails = $this->addSubtotalAmount($taxDetails, $taxDetailsItem);
            }
        }

        return $taxDetails;
    }

    /**
     * Get request for fetching address tax rate
     *
     * @param QuoteDetails $quoteDetails
     * @param int $storeId
     * @return \Magento\Framework\Object
     */
    protected function getAddressTaxRequest(QuoteDetails $quoteDetails, $storeId)
    {
        return $this->calculator->getRateRequest(
            $quoteDetails->getShippingAddress(),
            $quoteDetails->getBillingAddress(),
            $quoteDetails->getCustomerTaxClassId(),
            $storeId
        );
    }

    /**
     * Get request for fetching store tax rate
     *
     * @param int $storeId
     * @return \Magento\Framework\Object
     */
    protected function getStoreTaxRequest($storeId)
    {
        return $this->calculator->getRateOriginRequest($storeId);
    }

    /**
     * Calculate item price and row total with customized rounding level
     *
     * @param QuoteDetailsItem $item
     * @param \Magento\Framework\Object $taxRequest
     * @param int $storeId
     * @return TaxDetailsItem|null
     */
    protected function processItem(
        QuoteDetailsItem $item,
        \Magento\Framework\Object $taxRequest,
        $storeId
    ) {
        switch ($this->config->getAlgorithm($storeId)) {
            case Calculation::CALC_UNIT_BASE:
                return $this->unitBaseCalculation($item, $taxRequest, $storeId);
            case Calculation::CALC_ROW_BASE:
                return $this->rowBaseCalculation($item, $taxRequest, $storeId);
            case Calculation::CALC_TOTAL_BASE:
                return $this->totalBaseCalculation($item, $taxRequest, $storeId);
            default:
                return null;
        }
    }

    /**
     * Calculate item price and row total including/excluding tax based on unit price rounding level
     *
     * @param QuoteDetailsItem $item
     * @param \Magento\Framework\Object $taxRequest
     * @param int $storeId
     * @return TaxDetailsItem
     */
    protected function unitBaseCalculation(
        QuoteDetailsItem $item,
        \Magento\Framework\Object $taxRequest,
        $storeId
    ) {
        $this->taxDetailsItemBuilder->setCode($item->getCode());
        $this->taxDetailsItemBuilder->setType($item->getType());
        $taxRequest->setProductClassId($item->getTaxClassId());
        $rate = $this->calculator->getRate($taxRequest);
        $this->taxDetailsItemBuilder->setTaxPercent($rate);
        $quantity = $item->getQuantity();
        $price = $taxPrice = $this->calculator->round($item->getUnitPrice());
        $subtotal = $taxSubtotal = $this->calculator->round($item->getRowTotal());
        if ($item->getTaxIncluded()) {
            if ($taxRequest->getSameRateAsStore()) {
                $taxable = $price;
                $tax = $this->calculator->calcTaxAmount($taxable, $rate, true);
                $price = $price - $tax;
                $subtotal = $price * $quantity;
                $isPriceInclTax = true;
            } else {
                $storeRate = $this->calculator->getStoreRate($taxRequest, $storeId);
                $taxPrice = $this->calculatePriceInclTax($price, $storeRate, $rate);
                $taxable = $taxPrice;
                $tax = $this->calculator->calcTaxAmount($taxable, $rate, true, true);
                $price = $taxPrice - $tax;
                $taxSubtotal = $taxPrice * $quantity;
                $subtotal = $price * $quantity;
                $isPriceInclTax = true;
            }
        } else {
            $taxable = $price;
            $appliedRates = $this->calculator->getAppliedRates($taxRequest);
            $taxes = [];
            foreach ($appliedRates as $appliedRate) {
                $taxRate = $appliedRate['percent'];
                $taxes[] = $this->calculator->calcTaxAmount($taxable, $taxRate, false);
            }
            $tax = array_sum($taxes);
            $taxPrice = $price + $tax;
            $taxSubtotal = $taxPrice * $quantity;
            $isPriceInclTax = false;
        }
        $this->taxDetailsItemBuilder->setTaxAmount($tax * $quantity);
        $this->taxDetailsItemBuilder->setPrice($price);
        $this->taxDetailsItemBuilder->setPriceInclTax($taxPrice);
        $this->taxDetailsItemBuilder->setRowTotal($subtotal);
        $this->taxDetailsItemBuilder->setRowTotalInclTax($taxSubtotal);
        $this->taxDetailsItemBuilder->setTaxableAmount($taxable);
        return $this->taxDetailsItemBuilder->create();
    }

    /**
     * Calculate item price and row total including/excluding tax based on row total price rounding level
     *
     * @param QuoteDetailsItem $item
     * @param \Magento\Framework\Object $taxRequest
     * @param int $storeId
     * @return TaxDetailsItem
     */
    protected function rowBaseCalculation(
        QuoteDetailsItem $item,
        \Magento\Framework\Object $taxRequest,
        $storeId
    ) {

    }

    /**
     * Calculate item price and row total including/excluding tax based on total price rounding level
     *
     * @param QuoteDetailsItem $item
     * @param \Magento\Framework\Object $taxRequest
     * @param int $storeId
     * @return TaxDetailsItem
     */
    protected function totalBaseCalculation(
        QuoteDetailsItem $item,
        \Magento\Framework\Object $taxRequest,
        $storeId
    ) {

    }

    /**
     * Round price based on previous rounding operation delta
     *
     * @param float $price
     * @param string $rate
     * @param bool $direction
     * @param string $type
     * @return float
     */
    protected function deltaRound($price, $rate, $direction, $type = 'regular')
    {
        if ($price) {
            $rate = (string)$rate;
            $type = $type . $direction;
            // initialize the delta to a small number to avoid non-deterministic behavior with rounding of 0.5
            $delta = isset($this->roundingDeltas[$type][$rate]) ?
                $this->roundingDeltas[$type][$rate] :
                0.000001;
            $price += $delta;
            $roundPrice = $this->calculator->round($price);
            $this->roundingDeltas[$type][$rate] = $price - $roundPrice;
            $price = $roundPrice;
        }
        return $price;
    }

    /**
     * Recalculate row information for item based on children calculation
     *
     * @param TaxDetailsItem $parent
     * @param TaxDetailsItem[] $children
     * @return TaxDetailsItem
     */
    protected function recalculateParent(TaxDetailsItem $parent, $children)
    {
        $newParent = $this->taxDetailsItemBuilder->populate($parent);

        $price = 0.00;
        $price_incl_tax = 0.00;
        $row_total = 0.00;
        $row_total_incl_tax = 0.00;
        $tax_amount = 0.00;
        $taxable_amount = 0.00;
        $discount_amount = 0.00;
        $discount_tax_compensation_amount = 0.00;

        foreach($children as $child) {
            $price += $child->getPrice();
            $price_incl_tax += $child->getPriceInclTax();
            $row_total += $child->getRowTotal();
            $row_total_incl_tax += $child->getRowTotalInclTax();
            $tax_amount += $child->getTaxAmount();
            $taxable_amount += $child->getTaxableAmount();
            $discount_amount += $child->getDiscountAmount();
            $discount_tax_compensation_amount += $child->getDiscountTaxCompensationAmount();
        }

        $newParent->setPrice($price);
        $newParent->setPriceInclTax($price_incl_tax);
        $newParent->setRowTotal($row_total);
        $newParent->setRowTotalInclTax($row_total_incl_tax);
        $newParent->setTaxAmount($tax_amount);
        $newParent->setTaxableAmount($taxable_amount);
        $newParent->setDiscountAmount($discount_amount);
        $newParent->setDiscountTaxCompensationAmount($discount_tax_compensation_amount);

        return $newParent->create();
    }

    /**
     * Given a store price that includes tax at the store rate, this function will back out the store's tax, and add in
     * the customer's tax.  Returns this new price which is the customer's price including tax.
     *
     * @param float $storePriceInclTax
     * @param float $storeRate
     * @param float $customerRate
     * @return float
     */
    protected function calculatePriceInclTax($storePriceInclTax, $storeRate, $customerRate)
    {
        $storeTax = $this->calculator->calcTaxAmount($storePriceInclTax, $storeRate, true, false);
        $priceExclTax = $storePriceInclTax - $storeTax;
        $customerTax = $this->calculator->calcTaxAmount($priceExclTax, $customerRate, false, false);
        $customerPriceInclTax = $this->calculator->round($priceExclTax + $customerTax);
        return $customerPriceInclTax;
    }

    /**
     * Add row total item amount to subtotal
     *
     * @param array $taxDetailsData
     * @param TaxDetailsItem $item
     * @return array
     */
    protected function addSubtotalAmount($taxDetailsData, TaxDetailsItem $item)
    {
        $taxDetailsData[TaxDetails::KEY_SUBTOTAL]
            = $taxDetailsData[TaxDetails::KEY_SUBTOTAL] + $item->getRowTotal();

        $taxDetailsData[TaxDetails::KEY_TAX_AMOUNT]
            = $taxDetailsData[TaxDetails::KEY_TAX_AMOUNT] + $item->getTaxAmount();

        $taxDetailsData[TaxDetails::KEY_DISCOUNT_AMOUNT]
            = $taxDetailsData[TaxDetails::KEY_DISCOUNT_AMOUNT] + $item->getDiscountAmount();

        return $taxDetailsData;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\Resource\Sales\Order\Tax;
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
     * @param TaxDetailsBuilder $taxDetailsBuilder
     * @param TaxDetailsItemBuilder $taxDetailsItemBuilder
     */
    public function __construct(
        Calculation $calculation,
        \Magento\Tax\Model\Config $config,
        TaxDetailsBuilder $taxDetailsBuilder,
        TaxDetailsItemBuilder $taxDetailsItemBuilder
    ) {
        $this->calculator = $calculation;
        $this->config = $config;
        $this->taxDetailsBuilder = $taxDetailsBuilder;
        $this->taxDetailsItemBuilder = $taxDetailsItemBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateTax(QuoteDetails $quoteDetails, $storeId)
    {
        // initial TaxDetails data
        $taxDetailsData = [
            TaxDetails::KEY_SUBTOTAL => 0,
            TaxDetails::KEY_TAX_AMOUNT => 0,
            TaxDetails::KEY_TAXABLE_AMOUNT => 0,
            TaxDetails::KEY_DISCOUNT_AMOUNT => 0,
        ];

        $items = $quoteDetails->getItems();
        if (empty($items)) {
            return $this->taxDetailsBuilder->populateWithArray($taxDetailsData)->create();
        }

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
            if ((bool)$this->config->crossBorderTradeEnabled($storeId)) {
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
        $keyedItems = [];
        foreach ($items as $item) {
            $keyedItems[$item->getCode()] = $item;
        }
        $calculated = [];
        $processedItems = [];
        /** @var QuoteDetailsItem $item */
        foreach ($keyedItems as $key => $item) {
            if (in_array($key, $calculated)) {
                continue;
            }

            if ($item->getChildCodes()) {
                $processedChildren = [];
                foreach ($item->getChildCodes() as $childCode) {
                    $processedChildren[] = $this->processItem($keyedItems[$childCode], $addressRequest, $storeId);
                    $calculated[] = $childCode;
                }
                $processedItemBuilder = $this->calculateParent($processedChildren, $item->getQuantity());
                $processedItemBuilder->setCode($item->getCode());
                $processedItemBuilder->setType($item->getType());
                $processedItem = $processedItemBuilder->create();
            } else {
                $processedItem = $this->processItem($item, $addressRequest, $storeId);
            }
            $processedItems[] = $processedItem;
            $taxDetailsData = $this->addSubtotalAmount($taxDetailsData, $processedItem);
        }

        return $this->taxDetailsBuilder->populateWithArray($taxDetailsData)->setItems($processedItems)->create();
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
            } else {
                $storeRate = $this->calculator->getStoreRate($taxRequest, $storeId);
                $taxPrice = $this->calculatePriceInclTax($price, $storeRate, $rate);
                $taxable = $taxPrice;
                $tax = $this->calculator->calcTaxAmount($taxable, $rate, true, true);
                $price = $taxPrice - $tax;
                $taxSubtotal = $taxPrice * $quantity;
                $subtotal = $price * $quantity;
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
        $taxRequest->setProductClassId($item->getTaxClassId());
        $rate = $this->calculator->getRate($taxRequest);
        $quantity = $item->getQuantity();
        $price = $this->calculator->round($item->getUnitPrice());
        $subtotal = $this->calculator->round($item->getRowTotal());

        if ($item->getTaxIncluded()) {
            if ($taxRequest->getSameRateAsStore()) {
                $taxable = $subtotal;
                $rowTax = $this->calculator->calcTaxAmount($taxable, $rate, true, true);
                $taxPrice = $price;
                $taxSubtotal = $subtotal;
                $subtotal = $this->calculator->round($subtotal - $rowTax);
                $price = $this->calculator->round($subtotal / $quantity);
            } else {
                $storeRate = $this->calculator->getStoreRate($taxRequest, $storeId);
                $taxPrice = $this->calculatePriceInclTax($price, $storeRate, $rate);
                $tax = $this->calculator->calcTaxAmount($taxPrice, $rate, true, true);
                $price = $this->calculator->round($taxPrice - $tax);
                $taxable = $this->calculator->round($taxPrice * $quantity);
                $taxSubtotal = $this->calculator->round($taxPrice * $quantity);
                $rowTax = $this->calculator->calcTaxAmount($taxable, $rate, true, true);
                $subtotal = $this->calculator->round($taxSubtotal - $rowTax);
            }
        } else {
            $taxable = $subtotal;
            $appliedRates = $this->calculator->getAppliedRates($taxRequest);
            $rowTaxes = [];
            foreach ($appliedRates as $appliedRate) {
                $taxRate = $appliedRate['percent'];
                $rowTaxes[] = $this->calculator->calcTaxAmount($taxable, $taxRate, false, true);
            }
            $rowTax = array_sum($rowTaxes);
            $taxSubtotal = $this->calculator->round($subtotal + $rowTax);
            $taxPrice = $this->calculator->round($taxSubtotal / $quantity);
        }

        $this->taxDetailsItemBuilder->setPrice($price);
        $this->taxDetailsItemBuilder->setRowTotal($subtotal);
        $this->taxDetailsItemBuilder->setPriceInclTax($taxPrice);
        $this->taxDetailsItemBuilder->setRowTotalInclTax($taxSubtotal);
        $this->taxDetailsItemBuilder->setTaxableAmount($taxable);
        $this->taxDetailsItemBuilder->setCode($item->getCode());
        $this->taxDetailsItemBuilder->setType($item->getType());
        $this->taxDetailsItemBuilder->setTaxPercent($rate);

        return $this->taxDetailsItemBuilder->create();
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
        $taxRequest->setProductClassId($item->getTaxClassId());
        $rate = $this->calculator->getRate($taxRequest);
        $quantity = $item->getQuantity();
        $price = $this->calculator->round($item->getUnitPrice());
        $subtotal = $taxSubtotal = $this->calculator->round($item->getRowTotal());

        if ($item->getTaxIncluded()) {
            if ($taxRequest->getSameRateAsStore()) {
                $taxable = $subtotal;
                $rowTaxExact = $this->calculator->calcTaxAmount($taxable, $rate, true, false);
                $rowTax = $this->deltaRound($rowTaxExact, $rate, true);
                $subtotal = $subtotal - $rowTax;
                $taxPrice = $price;
                $price = $this->calculator->round($subtotal / $quantity);

            } else {
                $storeRate = $this->calculator->getStoreRate($taxRequest, $storeId);
                $taxPrice = $this->calculatePriceInclTax($price, $storeRate, $rate);
                $tax = $this->calculator->calcTaxAmount($taxPrice, $rate, true, true);
                $price = $taxPrice - $tax;
                $taxSubtotal = $taxable = $taxPrice * $quantity;
                $rowTax =
                    $this->deltaRound($this->calculator->calcTaxAmount($taxable, $rate, true, false), $rate, true);
                $subtotal = $taxSubtotal - $rowTax;
            }
        } else {
            $taxable = $subtotal;
            $appliedRates = $this->calculator->getAppliedRates($taxRequest);
            $rowTaxes = [];
            foreach ($appliedRates as $appliedRate) {
                $taxId = $appliedRate['id'];
                $taxRate = $appliedRate['percent'];
                $rowTaxes[] = $this->deltaRound(
                    $this->calculator->calcTaxAmount($taxable, $taxRate, false, false),
                    $taxId,
                    false
                );
            }
            $rowTax = array_sum($rowTaxes);
            $taxSubtotal = $subtotal + $rowTax;
            $taxPrice = $this->calculator->round($taxSubtotal / $quantity);
        }

        $this->taxDetailsItemBuilder->setTaxAmount($rowTax);
        $this->taxDetailsItemBuilder->setPrice($price);
        $this->taxDetailsItemBuilder->setPriceInclTax($taxPrice);
        $this->taxDetailsItemBuilder->setRowTotal($subtotal);
        $this->taxDetailsItemBuilder->setRowTotalInclTax($taxSubtotal);
        $this->taxDetailsItemBuilder->setTaxableAmount($taxable);
        $this->taxDetailsItemBuilder->setCode($item->getCode());
        $this->taxDetailsItemBuilder->setType($item->getType());
        $this->taxDetailsItemBuilder->setTaxPercent($rate);

        return $this->taxDetailsItemBuilder->create();
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
     * Calculate row information for item based on children calculation
     *
     * @param TaxDetailsItem[] $children
     * @param int $quantity
     * @return TaxDetailsItemBuilder
     */
    protected function calculateParent($children, $quantity)
    {
        $parentBuilder = $this->taxDetailsItemBuilder->populateWithArray([]);

        $rowTotal = 0.00;
        $rowTotalInclTax = 0.00;
        $taxAmount = 0.00;
        $taxableAmount = 0.00;
        $discountAmount = 0.00;
        $discountTaxCompensationAmount = 0.00;

        foreach ($children as $child) {
            $rowTotal += $child->getRowTotal();
            $rowTotalInclTax += $child->getRowTotalInclTax();
            $taxAmount += $child->getTaxAmount();
            $taxableAmount += $child->getTaxableAmount();
            $discountAmount += $child->getDiscountAmount();
            $discountTaxCompensationAmount += $child->getDiscountTaxCompensationAmount();
        }

        $price = $rowTotal / $quantity;
        $priceInclTax = $rowTotalInclTax / $quantity;

        $parentBuilder->setPrice($price);
        $parentBuilder->setPriceInclTax($priceInclTax);
        $parentBuilder->setRowTotal($rowTotal);
        $parentBuilder->setRowTotalInclTax($rowTotalInclTax);
        $parentBuilder->setTaxAmount($taxAmount);
        $parentBuilder->setTaxableAmount($taxableAmount);
        $parentBuilder->setDiscountAmount($discountAmount);
        $parentBuilder->setDiscountTaxCompensationAmount($discountTaxCompensationAmount);

        return $parentBuilder;
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

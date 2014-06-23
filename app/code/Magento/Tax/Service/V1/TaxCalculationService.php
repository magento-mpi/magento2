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
use Magento\Tax\Service\V1\Data\TaxDetails\AppliedTax;
use Magento\Tax\Service\V1\Data\TaxDetails\AppliedTaxRate;
use Magento\Tax\Service\V1\Data\TaxDetails\Item as TaxDetailsItem;
use Magento\Tax\Service\V1\Data\TaxDetails\ItemBuilder as TaxDetailsItemBuilder;
use Magento\Tax\Service\V1\Data\TaxDetailsBuilder;
use Magento\Store\Model\StoreManagerInterface;

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
     * Array to hold discount compensations for items
     *
     * @var array
     */
    protected $discountTaxCompensations;

    /**
     * Tax details item builder
     *
     * @var TaxDetailsBuilderItem
     */
    protected $taxDetailsItemBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Item code to Item object array.
     *
     * @var QuoteDetailsItem[]
     */
    private $keyedItems;

    /**
     * parent item code to children item array.
     *
     * @var QuoteDetailsItem[][]
     */
    private $parentToChildren;

    /**
     * Constructor
     *
     * @param Calculation $calculation
     * @param \Magento\Tax\Model\Config $config
     * @param TaxDetailsBuilder $taxDetailsBuilder
     * @param TaxDetailsItemBuilder $taxDetailsItemBuilder
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Calculation $calculation,
        \Magento\Tax\Model\Config $config,
        TaxDetailsBuilder $taxDetailsBuilder,
        TaxDetailsItemBuilder $taxDetailsItemBuilder,
        StoreManagerInterface $storeManager
    ) {
        $this->calculator = $calculation;
        $this->config = $config;
        $this->taxDetailsBuilder = $taxDetailsBuilder;
        $this->taxDetailsItemBuilder = $taxDetailsItemBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateTax(QuoteDetails $quoteDetails, $storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = $this->storeManager->getStore()->getStoreId();
        }

        // initial TaxDetails data
        $taxDetailsData = [
            TaxDetails::KEY_SUBTOTAL => 0.0,
            TaxDetails::KEY_TAX_AMOUNT => 0.0,
            TaxDetails::KEY_DISCOUNT_TAX_COMPENSATION_AMOUNT => 0.0,
            TaxDetails::KEY_APPLIED_TAXES => [],
            TaxDetails::KEY_ITEMS => [],
        ];

        $items = $quoteDetails->getItems();
        if (empty($items)) {
            return $this->taxDetailsBuilder->populateWithArray($taxDetailsData)->create();
        }
        $this->computeRelationships($items);

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

        // init rounding deltas for this quote
        $this->roundingDeltas = [];
        // init discount tax compensations array
        $this->discountTaxCompensations = [];
        $processedItems = [];
        /** @var QuoteDetailsItem $item */
        foreach ($this->keyedItems as $item) {
            if (isset($this->parentToChildren[$item->getCode()])) {
                $processedChildren = [];
                foreach ($this->parentToChildren[$item->getCode()] as $child) {
                    $processedItem = $this->processItem($child, $addressRequest, $storeId);
                    $taxDetailsData = $this->rollUp($taxDetailsData, $processedItem);
                    $processedItems[$processedItem->getCode()] = $processedItem;
                    $processedChildren[] = $processedItem;
                }
                $processedItemBuilder = $this->calculateParent($processedChildren, $item->getQuantity());
                $processedItemBuilder->setCode($item->getCode());
                $processedItemBuilder->setType($item->getType());
                $processedItem = $processedItemBuilder->create();
            } else {
                $processedItem = $this->processItem($item, $addressRequest, $storeId);
                $taxDetailsData = $this->rollUp($taxDetailsData, $processedItem);
            }
            $processedItems[$processedItem->getCode()] = $processedItem;
        }

        return $this->taxDetailsBuilder->populateWithArray($taxDetailsData)->setItems($processedItems)->create();
    }

    /**
     * Computes relationships between items, primarily the child to parent relationship.
     *
     * @param QuoteDetailsItem[] $items
     * @return void
     */
    private function computeRelationships($items)
    {
        $this->keyedItems = [];
        $this->parentToChildren = [];
        foreach ($items as $item) {
            if ($item->getParentCode() === null) {
                $this->keyedItems[$item->getCode()] = $item;
            } else {
                $this->parentToChildren[$item->getParentCode()][] = $item;
            }
        }
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
                return $this->totalBaseCalculation($item, $taxRequest, $storeId);
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
     * @param \Magento\Framework\Object $taxRateRequest
     * @param int $storeId
     * @return TaxDetailsItem
     */
    protected function unitBaseCalculation(
        QuoteDetailsItem $item,
        \Magento\Framework\Object $taxRateRequest,
        $storeId
    ) {
        /** @var  \Magento\Tax\Service\V1\Data\TaxDetails\AppliedTax[] $appliedTaxes */
        $appliedTaxes = [];
        $appliedTaxBuilder = $this->taxDetailsItemBuilder->getAppliedTaxBuilder();
        $appliedTaxRateBuilder = $appliedTaxBuilder->getAppliedTaxRateBuilder();

        $taxRateRequest->setProductClassId($item->getTaxClassId());
        $appliedRates = $this->calculator->getAppliedRates($taxRateRequest);
        $rate = $this->calculator->getRate($taxRateRequest);

        $quantity = $this->getTotalQuantity($item);
        $price = $priceInclTax = $this->calculator->round($item->getUnitPrice());
        $rowTotal = $rowTotalInclTax = $this->calcRowTotal($item);

        $discountAmount = $item->getDiscountAmount();
        $applyTaxAfterDiscount = $this->config->applyTaxAfterDiscount($storeId);

        $discountTaxCompensationAmount = 0;

        $useDeltaRounding = false;
        if ($item->getTaxIncluded()) {
            if ($taxRateRequest->getSameRateAsStore()) {
                $tax = $this->calculator->calcTaxAmount($priceInclTax, $rate, true);
                $price = $priceInclTax - $tax;
                $rowTax = $tax * $quantity;
                $rowTotal = $price * $quantity;
            } else {
                $storeRate = $this->calculator->getStoreRate($taxRateRequest, $storeId);
                $priceInclTax = $this->calculatePriceInclTax($price, $storeRate, $rate);
                $tax = $this->calculator->calcTaxAmount($priceInclTax, $rate, true, true);
                $rowTax = $tax * $quantity;
                $price = $priceInclTax - $tax;
                $rowTotalInclTax = $priceInclTax * $quantity;
                $rowTotal = $price * $quantity;
            }

            //Handle discount
            if ($discountAmount && $applyTaxAfterDiscount ) {
                //TODO: handle originalDiscountAmount
                $unitDiscountAmount = $discountAmount / $quantity;
                $taxableAmount = max($priceInclTax - $unitDiscountAmount, 0);
                $unitTaxAfterDiscount = $this->calculator->calcTaxAmount(
                    $taxableAmount,
                    $rate,
                    true,
                    true
                );

                // Set discount tax compensation
                $unitDiscountTaxCompensationAmount = $tax - $unitTaxAfterDiscount;
                $discountTaxCompensationAmount = $unitDiscountTaxCompensationAmount * $quantity;
                $rowTax = $unitTaxAfterDiscount * $quantity;
            }

            //save applied taxes
            $appliedTaxes = $this->getAppliedTaxes($appliedTaxBuilder, $rowTax, $rate, $appliedRates);
        } else {
            $taxable = $price;
            $appliedRates = $this->calculator->getAppliedRates($taxRateRequest);
            $taxes = [];
            //Apply each tax rate separately
            foreach ($appliedRates as $appliedRate) {
                $taxId = $appliedRate['id'];
                $taxRate = $appliedRate['percent'];
                $unitTaxPerRate = $this->calculator->calcTaxAmount($taxable, $taxRate, false);
                $unitTaxAfterDiscount = $unitTaxPerRate;

                //Handle discount
                if ($discountAmount && $applyTaxAfterDiscount ) {
                    //TODO: handle originalDiscountAmount
                    $unitDiscountAmount = $discountAmount / $quantity;
                    $taxableAmount = max($priceInclTax - $unitDiscountAmount, 0);
                    $unitTaxAfterDiscount = $this->calculator->calcTaxAmount(
                        $taxableAmount,
                        $rate,
                        false,
                        true
                    );
                }
                $appliedTaxes[$appliedRate['id']] = $this->getAppliedTax(
                    $appliedTaxBuilder,
                    $unitTaxAfterDiscount * $quantity,
                    $appliedRate
                );

                $unitTaxes[] = $unitTaxAfterDiscount;
                $unitTaxesBeforeDiscount[] = $unitTaxPerRate;
            }
            $unitTax = array_sum($unitTaxes);
            $unitTaxBeforeDiscount = array_sum($unitTaxesBeforeDiscount);
            $rowTax = $unitTax * $quantity;
            $priceInclTax = $price + $unitTaxBeforeDiscount;
            $rowTotalInclTax = $priceInclTax * $quantity;
        }

        $this->taxDetailsItemBuilder->setCode($item->getCode());
        $this->taxDetailsItemBuilder->setTaxAmount($rowTax);
        $this->taxDetailsItemBuilder->setPrice($price);
        $this->taxDetailsItemBuilder->setPriceInclTax($priceInclTax);
        $this->taxDetailsItemBuilder->setRowTotal($rowTotal);
        $this->taxDetailsItemBuilder->setRowTotalInclTax($rowTotalInclTax);
        $this->taxDetailsItemBuilder->setCode($item->getCode());
        $this->taxDetailsItemBuilder->setType($item->getType());
        $this->taxDetailsItemBuilder->setTaxPercent($rate);
        $this->taxDetailsItemBuilder->setDiscountAmount($discountAmount);
        $this->taxDetailsItemBuilder->setDiscountTaxCompensationAmount($discountTaxCompensationAmount);

        $this->taxDetailsItemBuilder->setAppliedTaxes($appliedTaxes);
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
        $storeRate = $this->calculator->getStoreRate($taxRequest, $storeId);
        $rate = $this->calculator->getRate($taxRequest);
        $quantity = $this->getTotalQuantity($item);
        $price = $this->calculator->round($item->getUnitPrice());
        $subtotal = $this->calcRowTotal($item);

        if ($item->getTaxIncluded()) {
            if ($taxRequest->getSameRateAsStore() || ($rate == $storeRate)) {
                $taxable = $subtotal;
                $rowTax = $this->calculator->calcTaxAmount($taxable, $rate, true, true);
                $taxPrice = $price;
                $taxSubtotal = $subtotal;
                $subtotal = $this->calculator->round($subtotal - $rowTax);
                $price = $this->calculator->round($subtotal / $quantity);
            } else {
                $taxPrice = $this->calculatePriceInclTax($price, $storeRate, $rate);
                $tax = $this->calculator->calcTaxAmount($taxPrice, $rate, true, true);
                $price = $taxPrice - $tax;
                $taxable = $taxPrice * $quantity;
                $taxSubtotal = $taxPrice * $quantity;
                $rowTax = $this->calculator->calcTaxAmount($taxable, $rate, true, true);
                $subtotal = $taxSubtotal - $rowTax;
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
            $taxSubtotal = $subtotal + $rowTax;
            $taxPrice = $this->calculator->round($taxSubtotal / $quantity);
        }

        $this->taxDetailsItemBuilder->setPrice($price);
        $this->taxDetailsItemBuilder->setPriceInclTax($taxPrice);
        $this->taxDetailsItemBuilder->setRowTotal($subtotal);
        $this->taxDetailsItemBuilder->setRowTotalInclTax($taxSubtotal);
        $this->taxDetailsItemBuilder->setTaxableAmount($taxable);
        $this->taxDetailsItemBuilder->setCode($item->getCode());
        $this->taxDetailsItemBuilder->setType($item->getType());
        $this->taxDetailsItemBuilder->setTaxPercent($rate);
        $this->taxDetailsItemBuilder->setTaxAmount($rowTax);

        return $this->taxDetailsItemBuilder->create();
    }

    /**
     * Create AppliedTax data object based applied tax rates and tax amount
     *
     * @param \Magento\Tax\Service\V1\Data\TaxDetails\AppliedTaxBuilder $appliedTaxBuilder
     * @param float $rowTax
     * @param array $appliedRate
     * @return \Magento\Tax\Service\V1\Data\TaxDetails\AppliedTax
     */
    protected function getAppliedTax($appliedTaxBuilder, $rowTax, $appliedRate)
    {
        $appliedTaxRateBuilder = $appliedTaxBuilder->getAppliedTaxRateBuilder();
        $appliedTaxBuilder->setAmount($rowTax);
        $appliedTaxBuilder->setPercent($appliedRate['percent']);
        $appliedTaxBuilder->setTaxRateKey($appliedRate['id']);

        /** @var  AppliedTaxRate[] $rateDataObjects */
        $rateDataObjects = [];
        foreach ($appliedRate['rates'] as $rate) {
            $appliedTaxRateBuilder->setPercent($rate['percent']);
            $appliedTaxRateBuilder->setCode($rate['code']);
            $appliedTaxRateBuilder->setTitle($rate['title']);
            //Skipped position, priority and rule_id
            $rateDataObjects[$rate['code']] = $appliedTaxRateBuilder->create();
        }
        $appliedTaxBuilder->setRates($rateDataObjects);
        $appliedTax = $appliedTaxBuilder->create();
        return $appliedTax;
    }

    /**
     * Create AppliedTax data object based applied tax rates and tax amount
     *
     * @param \Magento\Tax\Service\V1\Data\TaxDetails\AppliedTaxBuilder $appliedTaxBuilder
     * @param float $rowTax
     * @param float $totalTaxRate
     * @param array $appliedRates
     * @return \Magento\Tax\Service\V1\Data\TaxDetails\AppliedTax[]
     */
    protected function getAppliedTaxes($appliedTaxBuilder, $rowTax, $totalTaxRate, $appliedRates)
    {
        /** @var \Magento\Tax\Service\V1\Data\TaxDetails\AppliedTax[] $appliedTaxes */
        $appliedTaxes = [];
        $appliedTaxRateBuilder = $appliedTaxBuilder->getAppliedTaxRateBuilder();
        $totalAppliedAmount = 0;
        foreach ($appliedRates as $appliedRate) {
            if ($appliedRate['percent'] == 0) {
                continue;
            }

            $appliedAmount = $rowTax / $totalTaxRate * $appliedRate['percent'];
            $appliedAmount = $this->calculator->round($appliedAmount);
            if ($totalAppliedAmount + $appliedAmount > $rowTax) {
                $appliedAmount = $rowTax - $totalAppliedAmount;
            }
            $totalAppliedAmount += $appliedAmount;

            $appliedTaxBuilder->setAmount($appliedAmount);
            $appliedTaxBuilder->setPercent($appliedRate['percent']);
            $appliedTaxBuilder->setTaxRateKey($appliedRate['id']);

            /** @var  AppliedTaxRate[] $rateDataObjects */
            $rateDataObjects = [];
            foreach ($appliedRate['rates'] as $rate) {
                $appliedTaxRateBuilder->setPercent($rate['percent']);
                $appliedTaxRateBuilder->setCode($rate['code']);
                $appliedTaxRateBuilder->setTitle($rate['title']);
                //Skipped position, priority and rule_id
                $rateDataObjects[$rate['code']] = $appliedTaxRateBuilder->create();
            }
            $appliedTaxBuilder->setRates($rateDataObjects);
            $appliedTax = $appliedTaxBuilder->create();
            $appliedTaxes[] = $appliedTax;
        }

        return $appliedTaxes;
    }

    /**
     * Calculate item price and row total including/excluding tax based on total price rounding level
     *
     * @param QuoteDetailsItem $item
     * @param \Magento\Framework\Object $taxRateRequest
     * @param int $storeId
     * @return TaxDetailsItem
     */
    protected function totalBaseCalculation(
        QuoteDetailsItem $item,
        \Magento\Framework\Object $taxRateRequest,
        $storeId
    ) {
        /** @var  \Magento\Tax\Service\V1\Data\TaxDetails\AppliedTax[] $appliedTaxes */
        $appliedTaxes = [];
        $appliedTaxBuilder = $this->taxDetailsItemBuilder->getAppliedTaxBuilder();
        $appliedTaxRateBuilder = $appliedTaxBuilder->getAppliedTaxRateBuilder();

        $taxRateRequest->setProductClassId($item->getTaxClassId());
        $appliedRates = $this->calculator->getAppliedRates($taxRateRequest);
        $rate = $this->calculator->getRate($taxRateRequest);

        $quantity = $this->getTotalQuantity($item);
        $price = $priceInclTax = $this->calculator->round($item->getUnitPrice());
        $rowTotal = $rowTotalInclTax = $taxableAmount = $this->calcRowTotal($item);

        $discountAmount = $item->getDiscountAmount();
        $applyTaxAfterDiscount = $this->config->applyTaxAfterDiscount($storeId);

        $discountTaxCompensationAmount = 0;

        $useDeltaRounding = false;
        if ($this->config->getAlgorithm($storeId) == Calculation::CALC_TOTAL_BASE) {
            $useDeltaRounding = true;
        }

        if ($item->getTaxIncluded()) {
            if ($taxRateRequest->getSameRateAsStore()) {
                $rowTaxExact = $this->calculator->calcTaxAmount($rowTotalInclTax, $rate, true, false);
                $rowTax = $this->round($rowTaxExact, $useDeltaRounding, $rate, true);
                $rowTotal = $rowTotalInclTax - $rowTax;
                $price = $this->calculator->round($rowTotal / $quantity);
            } else {
                $storeRate = $this->calculator->getStoreRate($taxRateRequest, $storeId);
                $priceInclTax = $this->calculatePriceInclTax($price, $storeRate, $rate);
                $rowTotalInclTax = $priceInclTax * $quantity;
                $rowTax =
                    $this->round(
                        $this->calculator->calcTaxAmount($rowTotalInclTax, $rate, true, false),
                        $useDeltaRounding,
                        $rate,
                        true
                    );
                $rowTotal = $rowTotalInclTax - $rowTax;
                $price = $this->calculator->round($rowTotal / $quantity);
            }

            //Handle discount
            if ($discountAmount && $applyTaxAfterDiscount ) {
                //TODO: handle originalDiscountAmount
                $taxableAmount = max($taxableAmount - $discountAmount, 0);
                $rowTaxAfterDiscount = $this->calculator->calcTaxAmount(
                    $taxableAmount,
                    $rate,
                    true,
                    false
                );
                //Round the row tax using a different type so that we don't pollute the rounding deltas
                $rowTaxAfterDiscount = $this->round(
                    $rowTaxAfterDiscount,
                    $useDeltaRounding,
                    $rate,
                    true,
                    'tax_after_discount'
                );

                // Set discount tax compensation
                $discountTaxCompensationAmount = $rowTax - $rowTaxAfterDiscount;
                $rowTax = $rowTaxAfterDiscount;
            }

            //save applied taxes
            $appliedTaxes = $this->getAppliedTaxes($appliedTaxBuilder, $rowTax, $rate, $appliedRates);
        } else {
            $taxableAmount = $rowTotal;
            $appliedRates = $this->calculator->getAppliedRates($taxRateRequest);
            $rowTaxes = [];
            //Apply each tax rate separately
            foreach ($appliedRates as $appliedRate) {
                $taxId = $appliedRate['id'];
                $taxRate = $appliedRate['percent'];
                $rowTaxPerRate = $this->round(
                    $this->calculator->calcTaxAmount($taxableAmount, $taxRate, false, false),
                    $useDeltaRounding,
                    $taxId,
                    false
                );
                $rowTaxAfterDiscount = $rowTaxPerRate;
                //Handle discount
                if ($discountAmount && $applyTaxAfterDiscount ) {
                    //TODO: handle originalDiscountAmount
                    $taxableAmount = max($rowTotal - $discountAmount, 0);
                    $rowTaxAfterDiscount = $this->calculator->calcTaxAmount(
                        $taxableAmount,
                        $taxRate,
                        false,
                        false
                    );
                    //Round the row tax using a different type so that we don't pollute the rounding deltas
                    $rowTaxAfterDiscount = $this->round(
                        $rowTaxAfterDiscount,
                        $useDeltaRounding,
                        $taxRate,
                        false,
                        'tax_after_discount'
                    );
                }
                $appliedTaxes[$appliedRate['id']] = $this->getAppliedTax(
                    $appliedTaxBuilder,
                    $rowTaxAfterDiscount,
                    $appliedRate
                );
                $rowTaxes[] = $rowTaxAfterDiscount;
                $rowTaxesBeforeDiscount[] = $rowTaxPerRate;
            }
            $rowTax = array_sum($rowTaxes);
            $rowTaxBeforeDiscount = array_sum($rowTaxesBeforeDiscount);
            $rowTotalInclTax = $rowTotal + $rowTaxBeforeDiscount;
            $priceInclTax = $this->calculator->round($rowTotalInclTax / $quantity);
        }

        $this->taxDetailsItemBuilder->setCode($item->getCode());
        $this->taxDetailsItemBuilder->setTaxAmount($rowTax);
        $this->taxDetailsItemBuilder->setPrice($price);
        $this->taxDetailsItemBuilder->setPriceInclTax($priceInclTax);
        $this->taxDetailsItemBuilder->setRowTotal($rowTotal);
        $this->taxDetailsItemBuilder->setRowTotalInclTax($rowTotalInclTax);
        $this->taxDetailsItemBuilder->setTaxableAmount($taxableAmount);
        $this->taxDetailsItemBuilder->setCode($item->getCode());
        $this->taxDetailsItemBuilder->setType($item->getType());
        $this->taxDetailsItemBuilder->setTaxPercent($rate);
        $this->taxDetailsItemBuilder->setDiscountAmount($discountAmount);
        $this->taxDetailsItemBuilder->setDiscountTaxCompensationAmount($discountTaxCompensationAmount);

        $this->taxDetailsItemBuilder->setAppliedTaxes($appliedTaxes);
        return $this->taxDetailsItemBuilder->create();
    }

    /**
     * Round the value, use deltaRound if necessary
     *
     * @param float $price
     * @param bool $useDeltaRounding
     * @param string $rate
     * @param bool $direction
     * @param string $type
     * @return float
     */
    protected function round($price, $useDeltaRounding, $rate, $direction, $type = 'regular')
    {
        if ($useDeltaRounding) {
            return $this->deltaRound($price, $rate, $direction, $type);
        } else {
            return $this->calculator->round($price);
        }
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
        $rowTotal = 0.00;
        $rowTotalInclTax = 0.00;
        $taxAmount = 0.00;
        $taxableAmount = 0.00;

        foreach ($children as $child) {
            $rowTotal += $child->getRowTotal();
            $rowTotalInclTax += $child->getRowTotalInclTax();
            $taxAmount += $child->getTaxAmount();
            $taxableAmount += $child->getTaxableAmount();
        }

        $price = $this->calculator->round($rowTotal / $quantity);
        $priceInclTax = $this->calculator->round($rowTotalInclTax / $quantity);

        $this->taxDetailsItemBuilder->setPrice($price);
        $this->taxDetailsItemBuilder->setPriceInclTax($priceInclTax);
        $this->taxDetailsItemBuilder->setRowTotal($rowTotal);
        $this->taxDetailsItemBuilder->setRowTotalInclTax($rowTotalInclTax);
        $this->taxDetailsItemBuilder->setTaxAmount($taxAmount);
        $this->taxDetailsItemBuilder->setTaxableAmount($taxableAmount);

        return $this->taxDetailsItemBuilder;
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
    protected function rollUp($taxDetailsData, TaxDetailsItem $item)
    {
        $taxDetailsData[TaxDetails::KEY_SUBTOTAL]
            = $taxDetailsData[TaxDetails::KEY_SUBTOTAL] + $item->getRowTotal();

        $taxDetailsData[TaxDetails::KEY_TAX_AMOUNT]
            = $taxDetailsData[TaxDetails::KEY_TAX_AMOUNT] + $item->getTaxAmount();

        $taxDetailsData[TaxDetails::KEY_DISCOUNT_TAX_COMPENSATION_AMOUNT] =
            $taxDetailsData[TaxDetails::KEY_DISCOUNT_TAX_COMPENSATION_AMOUNT]
            + $item->getDiscountTaxCompensationAmount();

        $itemAppliedTaxes = $item->getAppliedTaxes();
        if (!isset($taxDetailsData[TaxDetails::KEY_APPLIED_TAXES])) {
            $taxDetailsData[TaxDetails::KEY_APPLIED_TAXES] = [];
        }
        $appliedTaxes = $taxDetailsData[TaxDetails::KEY_APPLIED_TAXES];
        foreach ($itemAppliedTaxes as $taxId => $itemAppliedTax) {
            if (!isset($appliedTaxes[$taxId])) {
                //convert rate data object to array
                $rates = [];
                $rateDataObjects = $itemAppliedTax->getRates();
                foreach ($rateDataObjects as $rateDataObject) {
                    $rates[$rateDataObject->getCode()] = [
                        'code' => $rateDataObject->getCode(),
                        'title' => $rateDataObject->getTitle(),
                        'percent' => $rateDataObject->getPercent(),
                    ];
                }
                $appliedTaxes[$taxId] = [
                    'amount' => $itemAppliedTax->getAmount(),
                    'percent' => $itemAppliedTax->getPercent(),
                    'rates' => $rates,
                    'tax_rate_key' => $itemAppliedTax->getTaxRateKey(),
                ];
            } else {
                $appliedTaxes[$taxId]['amount'] += $itemAppliedTax->getAmount();
            }
        }

        $taxDetailsData[TaxDetails::KEY_APPLIED_TAXES] = $appliedTaxes;
        return $taxDetailsData;
    }

    /**
     * Calculates the total quantity for this item.
     *
     * What this really means is that if this is a child item, it return the parent quantity times
     * the child quantity and return that as the child's quantity.
     *
     * @param QuoteDetailsItem $item
     * @return float
     */
    protected function getTotalQuantity(QuoteDetailsItem $item)
    {
        if ($item->getParentCode()) {
            $parentQuantity = $this->keyedItems[$item->getParentCode()]->getQuantity();
            return $parentQuantity * $item->getQuantity();
        }
        return $item->getQuantity();
    }

    /**
     * Calculate the row total for an item
     *
     * @param QuoteDetailsItem $item
     * @return float
     */
    protected function calcRowTotal(QuoteDetailsItem $item)
    {
        $qty = $this->getTotalQuantity($item);

        // Round unit price before multiplying to prevent losing 1 cent on subtotal
        $total = $this->calculator->round($item->getUnitPrice()) * $qty;

        return $this->calculator->round($total);
    }
}

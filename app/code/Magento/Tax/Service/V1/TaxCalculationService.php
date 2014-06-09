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
     * @var Data\TaxDetailsBuilder
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
     */
    protected $roundingDeltas;

    /**
     * Constructor
     *
     * @param Calculation $calculation
     * @param \Magento\Tax\Model\Config $config
     * @param \Magento\Tax\Helper\Data $helper
     * @param Data\TaxDetailsBuilder $taxDetailsBuilder
     */
    public function __construct(
        Calculation $calculation,
        \Magento\Tax\Model\Config $config,
        \Magento\Tax\Helper\Data $helper,
        TaxDetailsBuilder $taxDetailsBuilder
    ) {
        $this->calculator = $calculation;
        $this->config = $config;
        $this->helper = $helper;
        $this->taxDetailsBuilder = $taxDetailsBuilder;
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
            $quoteDetails->getShippingAddress() ? $quoteDetails->getShippingAddress() : false,
            $quoteDetails->getBillingAddress() ? $quoteDetails->getBillingAddress() : false,
            $quoteDetails->getCustomerTaxClassId() ? $quoteDetails->getCustomerTaxClassId() : false,
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

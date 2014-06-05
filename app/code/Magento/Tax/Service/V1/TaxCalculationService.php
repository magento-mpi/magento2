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
use Magento\Tax\Service\V1\Data\TaxDetails;

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
    protected $calculator = null;

    /**
     * Tax configuration object
     *
     * @var \Magento\Tax\Model\Config
     */
    protected $config = null;

    /**
     * Constructor
     *
     * @param Calculation $calculation
     * @param \Magento\Tax\Model\Config $config
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Calculation $calculation,
        \Magento\Tax\Model\Config $config,
        StoreManagerInterface $storeManager
    ) {
        $this->calculator = $calculation;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateTax(QuoteDetails $quoteDetails, $storeId)
    {

    }

    /**
     * Get request for fetching address tax rate
     *
     * @param Data\QuoteDetails $quoteDetails
     * @param int $storeId
     * @return \Magento\Framework\Object
     */
    protected function getAddressTaxRequest($quoteDetails, $storeId)
    {

    }

    /**
     * Get request for fetching store tax rate
     *
     * @param Data\QuoteDetails $quoteDetails
     * @param int $storeId
     * @return \Magento\Framework\Object
     */
    protected function getStoreTaxRequest($quoteDetails, $storeId)
    {

    }

    /**
     * Calculate item price and row total with customized rounding level
     *
     * @param Data\QuoteDetails\Item $item
     * @param \Magento\Framework\Object $taxRequest
     * @param int $storeId
     * @return Data\TaxDetails|null
     */
    protected function processItem($item, $taxRequest, $storeId)
    {
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
     * @param Data\QuoteDetails\Item $item
     * @param \Magento\Framework\Object $taxRequest
     * @param int $storeId
     * @return Data\TaxDetails
     */
    protected function unitBaseCalculation($item, $taxRequest, $storeId)
    {

    }

    /**
     * Calculate item price and row total including/excluding tax based on row total price rounding level
     *
     * @param Data\QuoteDetails\Item $item
     * @param \Magento\Framework\Object $taxRequest
     * @param int $storeId
     * @return Data\TaxDetails
     */
    protected function rowBaseCalculation($item, $taxRequest, $storeId)
    {

    }

    /**
     * Calculate item price and row total including/excluding tax based on total price rounding level
     *
     * @param Data\QuoteDetails\Item $item
     * @param \Magento\Framework\Object $taxRequest
     * @param int $storeId
     * @return Data\TaxDetails
     */
    protected function totalBaseCalculation($item, $taxRequest, $storeId)
    {

    }

    /**
     * Given a store price that includes tax at the store rate, this function will back out the store's tax, and add in
     * the customer's tax.  Returns this new price which is the customer's price including tax.
     *
     * @param float $storePriceInclTax
     * @param float $storeRate
     * @param float $customerRate
     *
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

}

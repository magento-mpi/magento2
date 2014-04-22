<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Pricing;

use Magento\Pricing\Adjustment\AdjustmentInterface;
use Magento\Pricing\Object\SaleableInterface;
use Magento\Tax\Helper\Data as TaxHelper;

/**
 * Tax pricing adjustment model
 */
class Adjustment implements AdjustmentInterface
{
    /**
     * Adjustment code tax
     */
    const ADJUSTMENT_CODE = 'tax';

    /**
     * @var TaxHelper
     */
    protected $taxHelper;

    /**
     * @var int|null
     */
    protected $sortOrder;

    /**
     * @param TaxHelper $taxHelper
     * @param int $sortOrder
     */
    public function __construct(TaxHelper $taxHelper, $sortOrder = null)
    {
        $this->taxHelper = $taxHelper;
        $this->sortOrder = $sortOrder;
    }

    /**
     * Get adjustment code
     *
     * @return string
     */
    public function getAdjustmentCode()
    {
        return self::ADJUSTMENT_CODE;
    }

    /**
     * Define if adjustment is included in base price
     *
     * @return bool
     */
    public function isIncludedInBasePrice()
    {
        return $this->taxHelper->priceIncludesTax();
    }

    /**
     * Define if adjustment is included in display price
     *
     * @return bool
     */
    public function isIncludedInDisplayPrice()
    {
        return $this->taxHelper->displayPriceIncludingTax() || $this->taxHelper->displayBothPrices();
    }

    /**
     * Extract adjustment amount from the given amount value
     *
     * @param float $amount
     * @param SaleableInterface $saleableItem
     * @return float
     */
    public function extractAdjustment($amount, SaleableInterface $saleableItem)
    {
        if ($this->taxHelper->priceIncludesTax()) {
            $adjustedAmount = $this->taxHelper->getPrice($saleableItem, $amount);
            $result = $amount - $adjustedAmount;
        } else {
            $result = 0.;
        }
        return $result;
    }

    /**
     * Apply adjustment amount and return result value
     *
     * @param float $amount
     * @param SaleableInterface $saleableItem
     * @return float
     */
    public function applyAdjustment($amount, SaleableInterface $saleableItem)
    {
        $includingTax = !$this->taxHelper->priceIncludesTax();
        return $this->taxHelper->getPrice($saleableItem, $amount, $includingTax);
    }

    /**
     * Check if adjustment should be excluded from calculations along with the given adjustment
     *
     * @param string $adjustmentCode
     * @return bool
     */
    public function isExcludedWith($adjustmentCode)
    {
        return $this->getAdjustmentCode() === $adjustmentCode;
    }

    /**
     * Return sort order position
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
}

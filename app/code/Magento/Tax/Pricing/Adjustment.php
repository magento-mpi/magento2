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
    const CODE = 'tax';

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
     * @return string
     */
    public function getAdjustmentCode()
    {
        return self::CODE;
    }

    /**
     * @return bool
     */
    public function isIncludedInBasePrice()
    {
        return $this->taxHelper->priceIncludesTax();
    }

    /**
     * {@inheritdoc}
     */
    public function isIncludedInDisplayPrice()
    {
        return $this->taxHelper->displayPriceIncludingTax() || $this->taxHelper->displayBothPrices();
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function applyAdjustment($amount, SaleableInterface $saleableItem)
    {
        $includingTax = !$this->taxHelper->priceIncludesTax();
        $amount = $this->taxHelper->getPrice($saleableItem, $amount, $includingTax);
        return $amount;
    }

    /**
     * {@inheritdoc}
     */
    public function isExcludedWith($adjustmentCode)
    {
        return $this->getAdjustmentCode() === $adjustmentCode;
    }

    /**
     * Get sort order position
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

use Magento\Pricing\Adjustment\AdjustmentInterface;
use Magento\Pricing\Price\PriceInterface;
use Magento\Pricing\PriceInfoInterface;
use Magento\Pricing\Object\SaleableInterface;

/**
 * Abstract catalog price model
 */
class AbstractPrice implements PriceInterface
{
    /**
     * Default product quantity
     */
    const PRODUCT_QUANTITY_DEFAULT = 1.;

    /**
     * @var string
     */
    protected $priceType;

    /**
     * @var SaleableInterface
     */
    protected $salableItem;

    /**
     * @var PriceInfoInterface
     */
    protected $priceInfo;

    /**
     * @var AdjustmentInterface[]
     */
    protected $adjustments;

    /**
     * @var float
     */
    protected $baseAmount = 0.00;

    /**
     * @var float
     */
    protected $adjustedAmount = 0.00;

    /**
     * @var float[]
     */
    protected $adjustedAmounts = [];

    /**
     * @var float
     */
    protected $quantity;

    /**
     * @param SaleableInterface $salableItem
     * @param float $quantity
     */
    public function __construct(SaleableInterface $salableItem, $quantity)
    {
        $this->salableItem = $salableItem;
        $this->quantity = $quantity;
        $this->priceInfo = $salableItem->getPriceInfo();
        $this->baseAmount = $this->getValue();

        $adjustments = [];
        foreach (array_reverse($this->priceInfo->getAdjustments()) as $adjustment) {
            /** @var AdjustmentInterface $adjustment */
            if ($adjustment->isIncludedInBasePrice()) {
                $code = $adjustment->getAdjustmentCode();
                $adjustments[$code] = $adjustment;
                $adjustedAmount = $adjustment->extractAdjustment($this->baseAmount, $this->salableItem);
                $this->baseAmount = $this->baseAmount - $adjustedAmount;
                $this->adjustedAmount += $adjustedAmount;
                $this->adjustedAmounts[$code] = $adjustedAmount;
            }
        }
        $this->adjustments = $adjustments;
    }

    /**
     * Get PriceInfo Object
     *
     * @return \Magento\Pricing\PriceInfoInterface
     */
    protected function getPriceInfo()
    {
        return $this->salableItem->getPriceInfo();
    }

    /**
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceType()
    {
        return $this->priceType;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->salableItem->getDataUsingMethod($this->priceType);
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayValue($baseAmount = null, $excludedCode = null)
    {
        $amount = is_null($baseAmount) ? $this->baseAmount : $baseAmount;
        foreach ($this->priceInfo->getAdjustments() as $adjustment) {
            $code = $adjustment->getAdjustmentCode();
            $exclude = false;
            if ($excludedCode && $adjustment->isExcludedWith($excludedCode)) {
                $exclude = true;
            }
            if ($adjustment->isIncludedInDisplayPrice($this->salableItem) && !$exclude) {
                if (isset($this->adjustedAmounts[$code]) && is_null($baseAmount)) {
                    $amount = $amount + $this->adjustedAmounts[$code];
                } else {
                    $amount = $adjustment->applyAdjustment($amount, $this->salableItem);
                }
            }
        }
        return $amount;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->getDisplayValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseAmount()
    {
        return $this->baseAmount;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalAdjustmentAmount()
    {
        return $this->adjustedAmount;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustments()
    {
        return $this->adjustments;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustment($adjustmentCode)
    {
        return isset($this->adjustments[$adjustmentCode]) ? $this->adjustments[$adjustmentCode] : false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAdjustment($adjustmentCode)
    {
        return array_key_exists($adjustmentCode, $this->adjustments);
    }
}

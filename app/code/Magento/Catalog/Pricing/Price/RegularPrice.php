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
 * Class RegularPrice
 */
class RegularPrice implements PriceInterface
{
    /**
     * Default price type
     */
    const PRICE_TYPE_PRICE_DEFAULT = 'regular_price';

    /**
     * @var string
     */
    protected $priceType = self::PRICE_TYPE_PRICE_DEFAULT;

    /**
     * @var SaleableInterface|\Magento\Catalog\Model\Product
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
     * @var float|null
     */
    protected $baseAmount;

    /**
     * @var float|null
     */
    protected $adjustedAmount;

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
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->salableItem->getPrice();
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
}

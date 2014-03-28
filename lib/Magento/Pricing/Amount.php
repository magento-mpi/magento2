<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing;

use \Magento\Pricing\Object\SaleableInterface;

/**
 * Class Amount
 */
class Amount implements AmountInterface
{
    /**
     * @var Object\SaleableInterface
     */
    protected $saleableItem;

    /**
     * @var float
     */
    protected $amount;

    /**
     * @var float[]
     */
    protected $adjustedAmounts;

    /**
     * @var float[]
     */
    protected $displayAmounts;

    /**
     * @var float
     */
    protected $dirtyAmount;

    /**
     * @param AdjustmentComposite $adjustmentComposite
     * @param SaleableInterface $saleableItem
     * @param float $amount
     */
    public function __construct(
        AdjustmentComposite $adjustmentComposite,
        SaleableInterface $saleableItem,
        $amount
    ) {
        $this->saleableItem = $saleableItem;
        $this->adjustmentComposite = $adjustmentComposite;
        $this->dirtyAmount = $amount;
    }

    /**
     * Returns amount
     *
     * @return float
     */
    public function getAmount()
    {
        if (!$this->amount) {
            $this->amount = $this->dirtyAmount;
            foreach (array_reverse($this->adjustmentComposite->getAdjustments()) as $adjustment) {
                /** @var Adjustment\AdjustmentInterface $adjustment */
                if ($adjustment->isIncludedInBasePrice()) {
                    $adjustedAmount = $adjustment->extractAdjustment($this->amount, $this->saleableItem);
                    $this->amount = $this->amount - $adjustedAmount;
                    $this->adjustedAmounts[$adjustment->getAdjustmentCode()] = $adjustedAmount;
                }
            }
        }
        return $this->amount;
    }

    /**
     * Returns display amount
     *
     * @param null $excludedCode
     * @return float
     */
    public function getDisplayAmount($excludedCode = null)
    {
        $amount = $this->getAmount();
        foreach ($this->adjustmentComposite->getAdjustments() as $adjustment) {
            /** @var Adjustment\AdjustmentInterface $adjustment */
            if ($adjustment->isIncludedInDisplayPrice($this->saleableItem)
                && !($excludedCode && $adjustment->isExcludedWith($excludedCode))
            ) {
                if (isset($this->adjustedAmounts[$adjustment->getAdjustmentCode()])) {
                    $amount = $amount + $this->adjustedAmounts[$adjustment->getAdjustmentCode()];
                } else {
                    $amount = $adjustment->applyAdjustment($amount, $this->saleableItem);
                }
            }
        }
        return $amount;
    }
}

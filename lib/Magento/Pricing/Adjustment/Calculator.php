<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\Adjustment;

use Magento\Pricing\Amount\AmountFactory;
use Magento\Pricing\AdjustmentComposite;
use Magento\Pricing\Object\SaleableInterface;

/**
 * Class Calculator
 */
class Calculator
{
    /**
     * @var AmountFactory
     */
    protected $amountFactory;

    /**
     * @var \Magento\Pricing\AdjustmentComposite
     */
    protected $adjustmentComposite;

    /**
     * @param AmountFactory $amountFactory
     * @param AdjustmentComposite $adjustmentComposite
     */
    public function __construct(
        AmountFactory $amountFactory,
        AdjustmentComposite $adjustmentComposite
    ) {
        $this->adjustmentComposite = $adjustmentComposite;
        $this->amountFactory = $amountFactory;
    }

    /**
     * @param float|string $amount
     * @param SaleableInterface $saleableItem
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getAmount($amount, SaleableInterface $saleableItem)
    {
        $baseAmount = $fullAmount = $amount;
        $adjustments = [];
        foreach ($this->adjustmentComposite->getAdjustments() as $adjustment) {
            /** @var AdjustmentInterface $adjustment */
            $code = $adjustment->getAdjustmentCode();
            if ($adjustment->isIncludedInBasePrice()) {
                $adjust = $adjustment->extractAdjustment($baseAmount, $saleableItem);
                $baseAmount -= $adjust;
                $adjustments[$code] = $adjust;
            } elseif ($adjustment->isIncludedInDisplayPrice($saleableItem)) {
                $newAmount = $adjustment->applyAdjustment($fullAmount, $saleableItem);
                $adjust = $newAmount - $fullAmount;
                $adjustments[$code] = $adjust;
                $fullAmount = $newAmount;
            }
        }
        return $this->amountFactory->create($fullAmount, $adjustments);
    }
}

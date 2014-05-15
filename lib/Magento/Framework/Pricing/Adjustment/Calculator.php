<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Pricing\Adjustment;

use Magento\Framework\Pricing\Amount\AmountFactory;
use Magento\Framework\Pricing\Object\SaleableInterface;

/**
 * Class Calculator
 */
class Calculator implements CalculatorInterface
{
    /**
     * @var AmountFactory
     */
    protected $amountFactory;

    /**
     * @param AmountFactory $amountFactory
     */
    public function __construct(AmountFactory $amountFactory)
    {
        $this->amountFactory = $amountFactory;
    }

    /**
     * Retrieve Amount object based on given float amount, product and exclude option.
     * It is possible to pass "true" or adjustment code to exclude all or specific adjustment from an amount.
     *
     * @param float|string $amount
     * @param SaleableInterface $saleableItem
     * @param null|bool|string $exclude
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getAmount($amount, SaleableInterface $saleableItem, $exclude = null)
    {
        $baseAmount = $fullAmount = $amount;
        $adjustments = [];
        foreach ($saleableItem->getPriceInfo()->getAdjustments() as $adjustment) {
            $code = $adjustment->getAdjustmentCode();
            $toExclude = false;
            if ($exclude === true || ($exclude !== null && $code === $exclude)) {
                $toExclude = true;
            }
            if ($adjustment->isIncludedInBasePrice()) {
                $adjust = $adjustment->extractAdjustment($baseAmount, $saleableItem);
                $baseAmount -= $adjust;
                if (!$toExclude) {
                    $adjustments[$code] = $adjust;
                }
            } elseif ($adjustment->isIncludedInDisplayPrice($saleableItem)) {
                if ($toExclude) {
                    continue;
                }
                $newAmount = $adjustment->applyAdjustment($fullAmount, $saleableItem);
                $adjust = $newAmount - $fullAmount;
                $adjustments[$code] = $adjust;
                $fullAmount = $newAmount;
            }
        }

        return $this->amountFactory->create($fullAmount, $adjustments);
    }
}

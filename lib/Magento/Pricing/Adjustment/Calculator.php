<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\Adjustment;

use Magento\Pricing\Amount\AmountFactory;
use Magento\Pricing\Object\SaleableInterface;

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
     * @param float|string $amount
     * @param SaleableInterface $saleableItem
     * @param null $exclude
     * @return \Magento\Pricing\Amount\AmountInterface|mixed
     */
    public function getAmount($amount, SaleableInterface $saleableItem, $exclude = null)
    {
        $baseAmount = $fullAmount = $amount;
        $adjustments = [];
        foreach ($saleableItem->getPriceInfo()->getAdjustments() as $adjustment) {
            $code = $adjustment->getAdjustmentCode();
            if ($exclude !== null) {
                if ($code === $exclude) {
                    continue;
                }
            }
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

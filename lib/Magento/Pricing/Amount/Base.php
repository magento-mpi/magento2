<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\Amount;

use Magento\Pricing\Adjustment\AdjustmentInterface;

class Base implements AmountInterface
{
    /**
     * @var float
     */
    protected $amount;

    /**
     * @var float
     */
    protected $baseAmount;

    /**
     * @var float
     */
    protected $totalAdjustmentAmount;

    /**
     * @var float[]
     */
    protected $adjustmentAmounts = [];

    /**
     * @var AdjustmentInterface[]
     */
    protected $adjustments = [];

    /**
     * @param $amount
     * @param array $adjustmentAmounts
     */
    public function __construct(
        $amount,
        array $adjustmentAmounts = [])
    {
        $this->amount = $amount;
        $this->adjustmentAmounts = $adjustmentAmounts;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($exclude = null)
    {
        if ($exclude === null) {
            return $this->amount;
        } else {
            if (!is_array($exclude)) {
                $exclude = [(string)$exclude];
            }
            $amount = $this->amount;
            foreach ($exclude as $code) {
                if ($this->hasAdjustment($code)) {
                    $amount -= $this->adjustmentAmounts[$code];
                }
            }
            return $amount;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseAmount()
    {
        if ($this->baseAmount === null) {
            $this->calculateAmounts();
        }
        return $this->baseAmount;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustmentAmount($adjustmentCode)
    {
        return isset($this->adjustmentAmounts[$adjustmentCode])
            ? $this->adjustmentAmounts[$adjustmentCode]
            : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalAdjustmentAmount()
    {
        if ($this->totalAdjustmentAmount === null) {
            $this->calculateAmounts();
        }
        return $this->totalAdjustmentAmount;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustmentAmounts()
    {
        return $this->adjustmentAmounts;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAdjustment($adjustmentCode)
    {
        return array_key_exists($adjustmentCode, $this->adjustmentAmounts);
    }

    /**
     * Calculate base amount
     *
     * @return void
     */
    protected function calculateAmounts()
    {
        $this->baseAmount = $this->amount;
        $this->totalAdjustmentAmount = 0.;
        if ($this->adjustmentAmounts) {
            foreach ($this->adjustmentAmounts as $amount) {
                $this->baseAmount -= $amount;
                $this->totalAdjustmentAmount += $amount;
            }
        }
    }
}

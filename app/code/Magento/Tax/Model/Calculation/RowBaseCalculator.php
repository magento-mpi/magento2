<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Calculation;

use Magento\Tax\Model\Calculation;

class RowBaseCalculator extends AbstractAggregateCalculator
{
    /**
     * {@inheritdoc}
     */
    protected function roundAmount($amount, $rate = null, $direction = null, $type = self::KEY_REGULAR_DELTA_ROUNDING)
    {
        return $this->calculationTool->round($amount);
    }
}

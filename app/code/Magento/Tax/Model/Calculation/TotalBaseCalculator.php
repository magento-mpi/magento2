<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Calculation;

use Magento\Tax\Model\Calculation;

class TotalBaseCalculator extends AbstractAggregateCalculator
{
    /**
     * {@inheritdoc}
     */
    protected function roundAmount($amount, $rate = null, $direction = null, $type = self::KEY_REGULAR_DELTA_ROUNDING)
    {
        return $this->deltaRound($amount, $rate, $direction, $type);
    }
}

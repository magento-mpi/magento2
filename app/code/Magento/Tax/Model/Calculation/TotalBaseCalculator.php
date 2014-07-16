<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Calculation;

use Magento\Tax\Model\Calculation;
use Magento\Customer\Service\V1\Data\Address;
use Magento\Tax\Service\V1\Data\QuoteDetails\Item as QuoteDetailsItem;

class TotalBaseCalculator extends AggregateCalculator
{
    /**
     * {@inheritdoc}
     */
    protected function roundAmount($amount, $rate = null, $direction = null, $type = self::KEY_REGULAR_DELTA_ROUNDING)
    {
        return $this->deltaRound($amount, $rate, $direction, $type);
    }
}

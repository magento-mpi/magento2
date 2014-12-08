<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model;

class PeriodUnits implements \Magento\Framework\Option\ArrayInterface
{
    const DAY = 'day';

    const WEEK = 'week';

    const SEMI_MONTH = 'semi_month';

    const MONTH = 'month';

    const YEAR = 'year';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            self::DAY => __('Day'),
            self::WEEK => __('Week'),
            self::SEMI_MONTH => __('Two Weeks'),
            self::MONTH => __('Month'),
            self::YEAR => __('Year')
        ];
    }
}

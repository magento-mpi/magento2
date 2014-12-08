<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RecurringPayment\Test\Fixture;

use Magento\Catalog\Test\Fixture\SimpleProduct;
use Mtf\System\Config;

/**
 * Simple product fixture extended with recurring payment options
 */
class SimpleProductWithRecurringPayment extends SimpleProduct
{
    /**#@+
     * Placeholders
     */
    const RECURRING_PERIOD = 'recurring_period';
    const RECURRING_CYCLE = 'recurring_billing_cycle';
    /**#@-*/

    /**
     * Setup default values for placeholders
     *
     * @param Config $configuration
     * @param array $placeholders
     */
    public function __construct(Config $configuration, $placeholders = [])
    {
        $this->_placeholders[self::RECURRING_PERIOD] = 'day';
        $this->_placeholders[self::RECURRING_CYCLE] = '1';
        parent::__construct($configuration, $placeholders);
    }

    /**
     * Add recurring payment data
     *
     * @return array
     */
    protected function _getPreparedData()
    {
        $data = parent::_getPreparedData();
        $data['is_recurring'] = [
            'value' => 'Yes',
            'input_value' => 1,
            'group' => static::GROUP_PRODUCT_PRICING,
            'input' => 'select',
            'input_name' => 'product[is_recurring]',
        ];
        $data['recurring_paymentperiod_unit'] = [
            'value' => 'Month',
            'input_value' => '%' . self::RECURRING_PERIOD . '%',
            'group' => static::GROUP_PRODUCT_PRICING,
            'input' => 'select',
            'input_name' => 'product[recurring_payment][period_unit]',
        ];
        $data['recurring_paymentperiod_frequency'] = [
            'value' => '%' . self::RECURRING_CYCLE . '%',
            'group' => static::GROUP_PRODUCT_PRICING,
            'input_name' => 'product[recurring_payment][period_frequency]',
        ];
        return $data;
    }
}

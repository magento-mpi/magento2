<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\RecurringPayment\Setup\Product;

class Converter extends \Magento\Tools\SampleData\Module\Catalog\Setup\Product\Converter
{
    /**
     * Template for the recurring payment data
     * @var array
     */
    protected $recurringPayment = array(
        'start_date_is_editable' => '1',
        'period_unit' => 'month',
        'period_frequency' => '',
        'period_max_cycles' => '12',
        'schedule_description' => 'Schedule subscription',
        'suspension_threshold' => '',
        'bill_failed_later' => '0',
        'trial_period_unit' => 'month',
        'trial_period_frequency' => '1',
        'trial_period_max_cycles' => '1',
        'trial_billing_amount' => '',
        'init_amount' => '',
        'init_may_fail' => ''
    );

    /**
     * Convert CSV format row to array
     *
     * @param array $row
     * @return array
     */
    public function convertRow($row)
    {
        $data = parent::convertRow($row);
        if (!isset($data['is_recurring']) || $data['is_recurring'] != '1') {
            unset($data['recurring_payment']);
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    protected function convertField(&$data, $field, $value)
    {
        if (!isset($data['recurring_payment'])) {
            $data['recurring_payment'] = $this->recurringPayment;
        }
        if (isset($this->recurringPayment[$field])) {
            $data['recurring_payment'][$field] = $value;
            return true;
        }
        return false;
    }
}

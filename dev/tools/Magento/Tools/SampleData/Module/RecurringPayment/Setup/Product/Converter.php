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
     * @param $row
     * @return array
     */
    public function convertRow($row)
    {
        $recurringData = $this->recurringPayment;
        $data = [];
        foreach ($row as $field => $value) {
            if ('category' == $field) {
                $data['category_ids'] = $this->getCategoryIds($this->getArrayValue($value));
                continue;
            }

            if ('qty' == $field) {
                $data['quantity_and_stock_status'] = ['qty' => $value];
                continue;
            }

            if (isset($recurringData[$field])) {
                $recurringData[$field] = $value;
                continue;
            }

            $options = $this->getAttributeOptionValueIdsPair($field);
            if ($options) {
                $value = $this->getArrayValue($value);
                $result = [];
                foreach ($value as $v) {
                    if (isset($options[$v])) {
                        $result[] = $options[$v];
                    }
                }
                $value = count($result) == 1 ? current($result) : $result;
            }
            $data[$field] = $value;

        }
        if(isset($data['is_recurring']) && $data['is_recurring'] == '1')
        $data['recurring_payment'] = $recurringData;
        return $data;
    }
}

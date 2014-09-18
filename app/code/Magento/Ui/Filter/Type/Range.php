<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Filter\Type;

use \Magento\Ui\Filter\View;

/**
 * Class Range
 */
class Range extends View
{
    /**
     * Get condition by data type
     *
     * @param string|array $value
     * @return array|null
     */
    public function getCondition($value)
    {
        if (!empty($value['from']) || !empty($value['to'])) {
            if (isset($value['from']) && empty($value['from']) && $value['from'] !== '0') {
                $value['orig_from'] = $value['from'];
                $value['from'] = null;
            }
            if (isset($value['to']) && empty($value['to']) && $value['to'] !== '0') {
                $value['orig_to'] = $value['to'];
                $value['to'] = null;
            }
        } else {
            $value = null;
        }

        return $value;
    }
}

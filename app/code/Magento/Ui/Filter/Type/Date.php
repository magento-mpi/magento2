<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Filter\Type;

use Magento\Ui\Filter\FilterInterface;

/**
 * Class Date
 */
class Date implements FilterInterface
{
    /**
     * Get condition by data type
     *
     * @param string|array $value
     * @return array|null
     */
    public function getCondition($value)
    {
        $condition = null;
        if (!empty($value['from']) && !empty($value['to'])) {
            $condition = ['from' => $value['from'], 'to' => $value['to']];
        }

        return $condition;
    }
}

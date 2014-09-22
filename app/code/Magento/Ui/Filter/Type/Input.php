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
 * Class Input
 */
class Input extends View
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
        if (!empty($value) || is_numeric($value)) {
            $condition = ['like' => sprintf('%%%s%%', $value)];
        }

        return $condition;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component\Filter;

use Magento\Ui\Component\Form\Element\DataType\DataTypeInterface;

/**
 * Interface FilterInterface
 */
interface FilterInterface
{
    /**
     * Get condition by data type
     *
     * @param string|array $value
     * @return array|null
     */
    public function getCondition($value);
}

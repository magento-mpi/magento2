<?php
/**
 * {license}
 */

namespace Magento\Ui\Filter;

use Magento\Ui\DataType\DataTypeInterface;

/**
 * Interface FilterInterface
 * @package Magento\Ui\Filter
 */
interface FilterInterface
{
    /**
     * @return DataTypeInterface
     */
    public function getDataType();

    /**
     * @return array
     */
    public function getCondition();

    /**
     * @return string
     */
    public function render();
}

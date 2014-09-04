<?php
/**
 * {license}
 */

namespace Magento\Ui\Filter;

use Magento\Ui\DataType\DataTypeInterface;

/**
 * Class Filter
 * @package Magento\Ui\Filter
 */
class Filter implements FilterInterface
{
    /**
     * @return DataTypeInterface
     */
    public function getDataType()
    {
        //
    }

    /**
     * @return array
     */
    public function getCondition()
    {
        // eq, neq, in, nin, like, range, etc
    }

    /**
     * @return string
     */
    public function render()
    {
        return '';
    }
}

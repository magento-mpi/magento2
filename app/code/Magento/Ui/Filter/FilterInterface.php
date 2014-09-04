<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Filter;

use Magento\Ui\DataType\DataTypeInterface;

/**
 * Interface FilterInterface
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

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\DataType;

use Magento\Framework\Object;
use Magento\Ui\ViewInterface;

/**
 * Interface DataTypeInterface
 */
interface DataTypeInterface extends ViewInterface
{
    /**
     * Validate data
     *
     * @return bool
     */
    public function validate();

    /**
     * Get data object value
     *
     * @return mixed
     */
    public function getDataObjectValue();
}

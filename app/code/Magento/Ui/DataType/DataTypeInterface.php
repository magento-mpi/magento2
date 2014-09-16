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
    public function validate();

    public function getDataObjectValue();
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\DataType;

use Magento\Framework\Object;

/**
 * Interface DataTypeInterface
 */
interface DataTypeInterface
{
    public function getLabel();

    public function getDataObjectValueIndex();

    public function getSortable();

    public function render(Object $dataObject);

    public function getDataObjectValue(Object $dataObject);

    public function prepare($value, Object $dataObject);

    public function validate($value, Object $dataObject);
}

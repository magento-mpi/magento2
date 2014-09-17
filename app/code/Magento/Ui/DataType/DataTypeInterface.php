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
    /**
     * @return mixed
     */
    public function getLabel();

    /**
     * @return mixed
     */
    public function getDataObjectValueIndex();

    /**
     * @return mixed
     */
    public function getSortable();

    /**
     * @param \Magento\Framework\Object $dataObject
     * @return mixed
     */
    public function render(Object $dataObject);

    /**
     * @param \Magento\Framework\Object $dataObject
     * @return mixed
     */
    public function getDataObjectValue(Object $dataObject);

    /**
     * @param $value
     * @param \Magento\Framework\Object $dataObject
     * @return mixed
     */
    public function prepare($value, Object $dataObject);

    /**
     * @param $value
     * @param \Magento\Framework\Object $dataObject
     * @return mixed
     */
    public function validate($value, Object $dataObject);
}

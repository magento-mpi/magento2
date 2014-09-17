<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\DataType;

use Magento\Framework\Object as DataObject;

/**
 * Class AbstractDataType
 */
abstract class AbstractDataType implements DataTypeInterface
{
    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->configuration['label'];
    }

    /**
     * @return string
     */
    public function getDataObjectValueIndex()
    {
        return $this->configuration['index'];
    }

    /**
     * @return bool
     */
    public function getSortable()
    {
        return $this->configuration['sortable'];
    }

    /**
     * @param DataObject $dataObject
     * @return string
     */
    abstract public function render(DataObject $dataObject);

    /**
     * @param $value
     * @param DataObject $dataObject
     * @return string
     */
    public function prepare($value, DataObject $dataObject)
    {
        return '';
    }

    /**
     * @param $value
     * @param DataObject $dataObject
     * @return bool
     */
    public function validate($value, DataObject $dataObject)
    {
        return true;
    }

    /**
     * @param DataObject $dataObject
     * @return string
     */
    public function getDataObjectValue(DataObject $dataObject)
    {
        return $dataObject->getDataUsingMethod($this->getDataObjectValueIndex());
    }
}

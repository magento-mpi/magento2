<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Service;

use Magento\Service\Data\AbstractObject;
use Magento\Framework\Convert\ConvertArray;

class DataObjectConverter
{
    /**
     * Convert nested array into flat array.
     *
     * @param AbstractObject $dataObject
     * @return array
     */
    public static function toFlatArray(AbstractObject $dataObject)
    {
        $data = $dataObject->__toArray();
        return ConvertArray::toFlatArray($data);
    }

    /**
     * Convert keys to camelCase
     *
     * @param array $dataArray
     * @return \stdClass
     */
    public function convertKeysToCamelCase(array $dataArray)
    {
        $response = [];
        foreach ($dataArray as $fieldName => $fieldValue) {
            if (is_array($fieldValue) && !$this->_isSimpleSequentialArray($fieldValue)) {
                $fieldValue = $this->convertKeysToCamelCase($fieldValue);
            }
            $fieldName = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $fieldName))));
            $response[$fieldName] = $fieldValue;
        }
        return $response;
    }

    /**
     * Check if the array is a simple(one dimensional and not nested) and a sequential(non-associative) array
     *
     * @param array $data
     * @return bool
     */
    protected function _isSimpleSequentialArray(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_string($key) || is_array($value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Convert multidimensional object/array into multidimensional array of primitives.
     *
     * @param object|array $input
     * @param bool $removeItemNode Remove Item node from arrays if true
     * @return array
     * @throws \InvalidArgumentException
     */
    public function convertStdObjectToArray($input, $removeItemNode = false)
    {
        if (!is_object($input) && !is_array($input)) {
            throw new \InvalidArgumentException("Input argument must be an array or object");
        }
        if ($removeItemNode && isset($input->item)) {
            /**
             * In case when only one Data object value is passed, it will not be wrapped into a subarray
             * within item node. If several Data object values are passed, they will be wrapped into
             * an indexed array within item node.
             */
            $input = is_object($input->item) ? [$input->item] : $input->item;
        }
        $result = array();
        foreach ((array)$input as $key => $value) {
            if (is_object($value) || is_array($value)) {
                $result[$key] = $this->convertStdObjectToArray($value, $removeItemNode);
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }
}

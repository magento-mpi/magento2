<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Service;

use Magento\Service\Data\AbstractObject;
use Magento\Convert\ConvertArray;

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
     * Create new object and initialize its public fields with data retrieved from Data Object.
     *
     * This method processes all nested Data Objects recursively.
     *
     * @param AbstractObject $dataObject
     * @return \stdClass
     * @throws \InvalidArgumentException
     */
    public function toStdObject(AbstractObject $dataObject)
    {
        return $this->convertArrayToStdObject($dataObject->__toArray());
    }

    /**
     * Unpack as an array and convert keys to camelCase
     *
     * @param array $dataArray
     * @return \stdClass
     */
    public function convertArrayToStdObject(array $dataArray)
    {
        $response = new \stdClass();
        foreach ($dataArray as $fieldName => $fieldValue) {
            if (is_array($fieldValue) && !$this->_isSimpleSequentialArray($fieldValue)) {
                $fieldValue = $this->convertArrayToStdObject($fieldValue);
            }
            $fieldName = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $fieldName))));
            $response->$fieldName = $fieldValue;
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
     * @return array
     * @throws \InvalidArgumentException
     */
    public function convertStdObjectToArray($input)
    {
        if (!is_object($input) && !is_array($input)) {
            throw new \InvalidArgumentException("Input argument must be an array or object");
        }
        $result = array();
        foreach ((array)$input as $key => $value) {
            if (is_object($value) || is_array($value)) {
                $result[$key] = $this->convertStdObjectToArray($value);
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }
} 
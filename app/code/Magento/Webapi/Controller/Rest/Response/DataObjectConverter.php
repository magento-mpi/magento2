<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Webapi\Controller\Rest\Response;

use Magento\Framework\Service\Data\AbstractExtensibleObject;
use Magento\Framework\Service\ExtensibleDataObjectConverter;
use Magento\Webapi\Model\DataObjectProcessor;

/**
 * Data object converter for REST
 */
class DataObjectConverter
{
    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(DataObjectProcessor $dataObjectProcessor)
    {
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * Converts the incoming data into scalar or an array of scalars format.
     *
     * If the data provided is null, then an empty array is returned.  Otherwise, if the data is an object, it is
     * assumed to be a Data Object and converted to an associative array with keys representing the properties of the
     * Data Object.
     * Nested Data Objects are also converted.  If the data provided is itself an array, then we iterate through the
     * contents and convert each piece individually.
     *
     * @param mixed $data
     * @param string $dataType
     * @return array|int|string|bool|float Scalar or array of scalars
     */
    public function processServiceOutput($data, $dataType)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $datum) {
                if (is_object($datum)) {
                    $datum = $this->processDataObject(
                        $this->dataObjectProcessor->buildOutputDataArray($datum, $dataType)
                    );
                }
                $result[] = $datum;
            }
            return $result;
        } else if (is_object($data)) {
            return $this->processDataObject(
                $this->dataObjectProcessor->buildOutputDataArray($data, $dataType)
            );
        } else if (is_null($data)) {
            return [];
        } else {
            /** No processing is required for scalar types */
            return $data;
        }
    }

    /**
     * Convert data object to array and process available custom attributes
     *
     * @param array $dataObjectArray
     * @return array
     */
    protected function processDataObject($dataObjectArray)
    {
        if (isset($dataObjectArray[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY])) {
            $dataObjectArray = ExtensibleDataObjectConverter::convertCustomAttributesToSequentialArray(
                $dataObjectArray
            );
        }
        //Check for nested custom_attributes
        foreach ($dataObjectArray as $key => $value) {
            if (is_array($value)) {
                $dataObjectArray[$key] = $this->processDataObject($value);
            }
        }
        return $dataObjectArray;
    }
}

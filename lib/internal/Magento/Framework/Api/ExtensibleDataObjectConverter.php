<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Api;

use Magento\Framework\Api\AbstractExtensibleObject;
use Magento\Framework\Convert\ConvertArray;
use Magento\Framework\Api\AttributeValue;

/**
 * Class to convert Extensible Data Object array to flat array
 */
class ExtensibleDataObjectConverter
{
    /**
     * Convert AbstractExtensibleObject into flat array.
     *
     * @param AbstractExtensibleObject $dataObject
     * @param string[] $skipCustomAttributes
     * @return array
     */
    public static function toFlatArray(AbstractExtensibleObject $dataObject, $skipCustomAttributes = array())
    {
        $dataObjectArray = $dataObject->__toArray();
        //process custom attributes if present
        if (!empty($dataObjectArray[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY])) {
            /** @var AttributeValue[] $customAttributes */
            $customAttributes = $dataObjectArray[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY];
            unset ($dataObjectArray[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY]);
            foreach ($customAttributes as $attributeValue) {
                if (!in_array($attributeValue[AttributeValue::ATTRIBUTE_CODE], $skipCustomAttributes)) {
                    $dataObjectArray[$attributeValue[AttributeValue::ATTRIBUTE_CODE]]
                        = $attributeValue[AttributeValue::VALUE];
                }
            }
        }
        return ConvertArray::toFlatArray($dataObjectArray);
    }

    /**
     * Convert AbstractExtensibleObject into flat array.
     *
     * @param AbstractExtensibleObject $dataObject
     * @param string[] $skipCustomAttributes
     * @return array
     * @deprecated use toFlatArray instead. Should be removed once all references are refactored.
     */
    public static function toFlatArrayStatic(AbstractExtensibleObject $dataObject, $skipCustomAttributes = array())
    {
        $dataObjectArray = $dataObject->__toArray();
        //process custom attributes if present
        if (!empty($dataObjectArray[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY])) {
            /** @var AttributeValue[] $customAttributes */
            $customAttributes = $dataObjectArray[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY];
            unset ($dataObjectArray[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY]);
            foreach ($customAttributes as $attributeValue) {
                if (!in_array($attributeValue[AttributeValue::ATTRIBUTE_CODE], $skipCustomAttributes)) {
                    $dataObjectArray[$attributeValue[AttributeValue::ATTRIBUTE_CODE]]
                        = $attributeValue[AttributeValue::VALUE];
                }
            }
        }
        return ConvertArray::toFlatArray($dataObjectArray);
    }

    /**
     * Convert Extensible Data Object custom attributes in sequential array format.
     *
     * @param array $extensibleObjectData
     * @return array
     */
    public static function convertCustomAttributesToSequentialArray($extensibleObjectData)
    {
        $extensibleObjectData[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY] = array_values(
            $extensibleObjectData[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY]
        );
        return $extensibleObjectData;
    }
}

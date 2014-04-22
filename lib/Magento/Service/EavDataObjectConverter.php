<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Service;

use \Magento\Service\Data\Eav\AbstractObject;
use Magento\Convert\ConvertArray;
use Magento\Service\Data\Eav\AttributeValue;

/**
 * Class to convert Eav Data Object array to flat array
 */
class EavDataObjectConverter
{
    /**
     * Convert AbstractObject into flat array.
     *
     * @param AbstractObject $dataObject
     * @return array
     */
    public static function toFlatArray(AbstractObject $dataObject)
    {
        $dataObjectArray = $dataObject->__toArray();
        //process custom attributes if present
        if (!empty($dataObjectArray[AbstractObject::CUSTOM_ATTRIBUTES_KEY])) {
            /** @var AttributeValue[] $customAttributes */
            $customAttributes = $dataObjectArray[AbstractObject::CUSTOM_ATTRIBUTES_KEY];
            unset ($dataObjectArray[AbstractObject::CUSTOM_ATTRIBUTES_KEY]);
            foreach ($customAttributes as $attributeValue) {
                $dataObjectArray[$attributeValue[AttributeValue::ATTRIBUTE_CODE]]
                    = $attributeValue[AttributeValue::VALUE];
            }
        }
        return ConvertArray::toFlatArray($dataObjectArray);
    }

    /**
     * Convert Eav Data Object custom attributes in sequential array format.
     *
     * @param array $eavObjectData
     * @return array
     */
    public static function convertCustomAttributesToSequentialArray($eavObjectData)
    {
        $eavObjectData[AbstractObject::CUSTOM_ATTRIBUTES_KEY] = array_values(
            $eavObjectData[AbstractObject::CUSTOM_ATTRIBUTES_KEY]
        );
        return $eavObjectData;
    }
}

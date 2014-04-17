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

class EavDataObjectConverter
{
    /**
     * Convert nested array into flat array.
     *
     * @param AbstractObject $dataObject
     * @return array
     */
    public static function toFlatArray(AbstractObject $dataObject)
    {
        $customerAttributes = $dataObject->getCustomAttributes();
        $data = $dataObject->__toArray();
        if (empty($customerAttributes)) {
            return ConvertArray::toFlatArray($data);
        }
        /** @var AttributeValue[] $customAttributes */
        $customAttributes = $data[AbstractObject::CUSTOM_ATTRIBUTES_KEY];
        unset ($data[AbstractObject::CUSTOM_ATTRIBUTES_KEY]);
        foreach ($customAttributes as $attributeValue) {
            $data[$attributeValue[AttributeValue::ATTRIBUTE_CODE]] = $attributeValue[AttributeValue::VALUE];
        }

        return $data;
    }
}

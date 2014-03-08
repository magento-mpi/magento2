<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Data;

use Magento\Convert\ConvertArray;

/**
 * Class AddressConverter converts Address Service Data Object to an array
 */
class AddressConverter
{
    /**
     * Convert address data object to a flat array
     *
     * @param Address $addressDataObject
     * @return array
     */
    public static function toFlatArray(Address $addressDataObject)
    {
        // preserve street
        $street = $addressDataObject->getStreet();
        $addressArray = $addressDataObject->__toArray();
        // Unset street since it doesn't need to be processed by ConvertArray::toFlatArray
        unset($addressArray[Address::KEY_STREET]);
        $flatAddressArray = ConvertArray::toFlatArray($addressArray);
        $flatAddressArray[Address::KEY_STREET] = $street;
        return $flatAddressArray;
    }
} 
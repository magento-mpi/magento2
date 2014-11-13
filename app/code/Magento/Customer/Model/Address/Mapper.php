<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Address;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;

/**
 * Class AddressConverter converts Address Service Data Object to an array
 */
class Mapper
{
    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    private $extensibleDataObjectConverter;

    /**
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(ExtensibleDataObjectConverter $extensibleDataObjectConverter)
    {
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * Convert address data object to a flat array
     *
     * @param AddressInterface $addressDataObject
     * @return array
     * TODO:: Add concrete type of AddressInterface for $addressDataObject parameter once
     * all references have been refactored.
     */
    public function toFlatArray($addressDataObject)
    {
        $flatAddressArray = $this->extensibleDataObjectConverter->toFlatArray($addressDataObject);
        //preserve street
        $street = $addressDataObject->getStreet();
        if (!empty($street)) {
            // Unset flat street data
            $streetKeys = array_keys($street);
            foreach ($streetKeys as $key) {
                unset($flatAddressArray[$key]);
            }
            //Restore street as an array
            $flatAddressArray[AddressInterface::STREET] = $street;
        }
        return $flatAddressArray;
    }
}

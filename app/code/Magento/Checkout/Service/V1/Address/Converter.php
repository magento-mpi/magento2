<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Address;

use \Magento\Checkout\Service\V1\Data\Cart\Address;
use \Magento\Checkout\Service\V1\Data\Cart\AddressBuilder;
use \Magento\Checkout\Service\V1\Data\Cart\Address\Region;

class Converter
{
    /**
     * @var AddressBuilder
     */
    protected $addressBuilder;

    /**
     * @param AddressBuilder $addressBuilder
     */
    public function __construct(AddressBuilder $addressBuilder)
    {
        $this->addressBuilder = $addressBuilder;
    }

    /**
     * @param \Magento\Sales\Model\Quote\Address $address
     * @return \Magento\Checkout\Service\V1\Data\Cart\Address
     */
    public function convert(\Magento\Sales\Model\Quote\Address $address)
    {
        $data = [
            Address::KEY_COUNTRY_ID => $address->getCountryId(),
            Address::KEY_ID => $address->getId(),
            Address::KEY_CUSTOMER_ID => $address->getCustomerId(),
            Address::KEY_REGION => array(
                Region::KEY_REGION => $address->getRegion(),
                Region::KEY_REGION_ID => $address->getRegionId(),
                Region::KEY_REGION_CODE => $address->getRegionCode()
            ),
            Address::KEY_STREET => $address->getStreet(),
            Address::KEY_COMPANY => $address->getCompany(),
            Address::KEY_TELEPHONE => $address->getTelephone(),
            Address::KEY_FAX => $address->getFax(),
            Address::KEY_POSTCODE => $address->getPostcode(),
            Address::KEY_FIRSTNAME => $address->getFirstname(),
            Address::KEY_LASTNAME => $address->getLastname(),
            Address::KEY_MIDDLENAME => $address->getMiddlename(),
            Address::KEY_PREFIX => $address->getPrefix(),
            Address::KEY_SUFFIX => $address->getSuffix(),
            Address::KEY_EMAIL => $address->getEmail(),
            Address::KEY_VAT_ID => $address->getVatId()
        ];

        return $this->addressBuilder->populateWithArray($data)->create();
    }
}

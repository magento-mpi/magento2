<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Address;

use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Service\V1\Dto\Address;
use Magento\Customer\Service\V1\Dto\AddressBuilder;
use Magento\Customer\Model\Address as AddressModel;
use Magento\Customer\Service\V1\Dto\Region;
use Magento\Customer\Service\V1\Dto\RegionBuilder;
use \Magento\Customer\Service\V1\CustomerMetadataServiceInterface;

/**
 * Customer Address Model converter.
 *
 * Converts a Customer Address Model to a DTO.
 *
 * TODO: Remove this class after service refactoring is done and the model is no longer needed outside of service.
 *       Then this function could be moved to the service.
 */
class Converter
{
    /**
     * @var AddressBuilder
     */
    protected $_addressBuilder;

    /**
     * @var AddressFactory
     */
    protected $_addressFactory;

    /**
     * @var RegionBuilder
     */
    private $_regionBuilder;

    /**
     * @param AddressBuilder $addressBuilder
     * @param AddressFactory $addressFactory
     * @param RegionBuilder $regionBuilder
     */
    public function __construct(
        AddressBuilder $addressBuilder,
        AddressFactory $addressFactory,
        RegionBuilder $regionBuilder
    )
    {
        $this->_addressBuilder = $addressBuilder;
        $this->_addressFactory = $addressFactory;
        $this->_regionBuilder = $regionBuilder;
    }

    /**
     * Creates an address model out of an address DTO.
     *
     * @param Address $addressDto
     * @return AddressModel
     */
    public function createAddressModel(Address $addressDto)
    {
        $addressModel = $this->_addressFactory->create();
        $this->updateAddressModel($addressModel, $addressDto);

        return $addressModel;
    }

    /**
     * Updates an Address Model based on information from an Address DTO.
     *
     * @param AddressModel $addressModel
     * @param Address $address
     * return null
     */
    public function updateAddressModel(AddressModel $addressModel, Address $address)
    {
        // Set all attributes
        foreach ($address->getAttributes() as $attributeCode => $attributeData) {
            if (Address::KEY_REGION == $attributeCode && $address->getRegion() instanceof Region) {
                $addressModel->setDataUsingMethod(Address::KEY_REGION, $address->getRegion()->getRegion());
                $addressModel->setDataUsingMethod('region_code', $address->getRegion()->getRegionCode());
                $addressModel->setDataUsingMethod(Address::KEY_REGION_ID, $address->getRegion()->getRegionId());
            } else {
                $addressModel->setDataUsingMethod($attributeCode, $attributeData);
            }
        }
        // Set customer related data
        $isBilling = $address->isDefaultBilling();
        $addressModel->setIsDefaultBilling($isBilling);
        $addressModel->setIsDefaultShipping($address->isDefaultShipping());
        // Need to use attribute set or future updates can cause data loss
        if (!$addressModel->getAttributeSetId()) {
            $addressModel->setAttributeSetId(CustomerMetadataServiceInterface::ATTRIBUTE_SET_ID_ADDRESS);
        }
    }

    /**
     * Make address DTO out of an address model
     *
     * @param AddressModel $addressModel
     * @param int $defaultBillingId
     * @param int $defaultShippingId
     * @return Address
     */
    public function createAddressFromModel(AddressModel $addressModel, $defaultBillingId, $defaultShippingId)
    {
        $addressId = $addressModel->getId();
        $validAttributes = array_merge(
            $addressModel->getDefaultAttributeCodes(),
            [
                Address::KEY_ID, 'region_id', Address::KEY_REGION, Address::KEY_STREET, 'vat_is_valid',
                Address::KEY_DEFAULT_BILLING, Address::KEY_DEFAULT_SHIPPING, 'vat_request_id', 'vat_request_date',
                'vat_request_success'
            ]
        );
        $addressData = [];
        foreach ($addressModel->getAttributes() as $attribute) {
            $code = $attribute->getAttributeCode();
            if (!in_array($code, $validAttributes) && $addressModel->getData($code) !== null) {
                $addressData[$code] = $addressModel->getData($code);
            }
        }

        $region = $this->_regionBuilder->setRegionCode($addressModel->getRegionCode())
            ->setRegion($addressModel->getRegion())
            ->setRegionId($addressModel->getRegionId())
            ->create();
        $this->_addressBuilder->populateWithArray(array_merge($addressData, [
            Address::KEY_STREET => $addressModel->getStreet(),
            Address::KEY_ID => $addressId,
            Address::KEY_DEFAULT_BILLING => $addressId === $defaultBillingId,
            Address::KEY_DEFAULT_SHIPPING => $addressId === $defaultShippingId,
            Address::KEY_CUSTOMER_ID => $addressModel->getCustomerId(),
            Address::KEY_REGION => $region
        ]));

        $addressDto = $this->_addressBuilder->create();
        return $addressDto;
    }
}

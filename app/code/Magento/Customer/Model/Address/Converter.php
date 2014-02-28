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
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Customer\Service\V1\Dto\Region;
use Magento\Customer\Service\V1\Dto\RegionBuilder;
use Magento\Customer\Service\V1\CustomerMetadataServiceInterface;

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
     * Customer metadata service
     *
     * @var \Magento\Customer\Service\V1\CustomerMetadataServiceInterface
     */
    private $_metadataService;

    /**
     * @param AddressBuilder $addressBuilder
     * @param AddressFactory $addressFactory
     * @param RegionBuilder $regionBuilder
     * @param CustomerMetadataServiceInterface $metadataService
     */
    public function __construct(
        AddressBuilder $addressBuilder,
        AddressFactory $addressFactory,
        RegionBuilder $regionBuilder,
        CustomerMetadataServiceInterface $metadataService
    ) {
        $this->_addressBuilder = $addressBuilder;
        $this->_addressFactory = $addressFactory;
        $this->_regionBuilder = $regionBuilder;
        $this->_metadataService = $metadataService;
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
     * @param AbstractAddress $addressModel
     * @param int $defaultBillingId
     * @param int $defaultShippingId
     * @return Address
     */
    public function createAddressFromModel(AbstractAddress $addressModel, $defaultBillingId, $defaultShippingId)
    {
        $addressId = $addressModel->getId();

        $attributes = $this->_metadataService->getAllAddressAttributeMetadata();
        $addressData = [];
        foreach ($attributes as $attribute) {
            $code = $attribute->getAttributeCode();
            if (!is_null($addressModel->getData($code))) {
                $addressData[$code] = $addressModel->getData($code);
            }
        }

        $this->_addressBuilder->populateWithArray(array_merge($addressData, [
            Address::KEY_STREET => $addressModel->getStreet(),
            Address::KEY_ID => $addressId,
            Address::KEY_DEFAULT_BILLING => $addressId === $defaultBillingId,
            Address::KEY_DEFAULT_SHIPPING => $addressId === $defaultShippingId,
                    Address::KEY_CUSTOMER_ID =>
                        $addressModel->getCustomerId() ? $addressModel->getCustomerId() : $addressModel->getParentId(),
                    Address::KEY_REGION_CODE => $addressModel->getRegionCode(),
                ]));

        $addressDto = $this->_addressBuilder->create();
        return $addressDto;
    }
}

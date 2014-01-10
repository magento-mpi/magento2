<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

use Magento\Customer\Service\Entity\V1\AggregateException;
use Magento\Customer\Service\Entity\V1\Exception;
use Magento\Customer\Model\Address as CustomerAddressModel;

class CustomerAddressService implements CustomerAddressServiceInterface
{
    /** @var \Magento\Customer\Model\AddressFactory */
    private $_addressFactory;

    /**
     * @var \Magento\Customer\Model\Converter
     */
    private $_converter;

    /**
     * @var Dto\RegionBuilder
     */
    private $_regionBuilder;

    /**
     * @var Dto\AddressBuilder
     */
    private $_addressBuilder;


    /**
     * Constructor
     *
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Customer\Model\Converter $converter
     * @param Dto\RegionBuilder $regionBuilder
     * @param Dto\AddressBuilder $addressBuilder
     */
    public function __construct(
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\Converter $converter,
        Dto\RegionBuilder $regionBuilder,
        Dto\AddressBuilder $addressBuilder
    ) {
        $this->_addressFactory = $addressFactory;
        $this->_converter = $converter;
        $this->_regionBuilder = $regionBuilder;
        $this->_addressBuilder = $addressBuilder;
    }

    /**
     * @inheritdoc
     */
    public function getAddresses($customerId)
    {
        //TODO: use cache MAGETWO-16862
        $customer = $this->_converter->getCustomerModel($customerId);
        $addresses = $customer->getAddresses();
        $defaultBillingId = $customer->getDefaultBilling();
        $defaultShippingId = $customer->getDefaultShipping();

        $result = array();
        /** @var $address CustomerAddressModel */
        foreach ($addresses as $address) {
            $result[] = $this->_createAddress(
                $address,
                $defaultBillingId,
                $defaultShippingId
            );
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultBillingAddress($customerId)
    {
        //TODO: use cache MAGETWO-16862
        $customer = $this->_converter->getCustomerModel($customerId);
        $address = $customer->getDefaultBillingAddress();
        if ($address === false) {
            return null;
        }
        return $this->_createAddress(
            $address,
            $customer->getDefaultBilling(),
            $customer->getDefaultShipping()
        );
    }

    /**
     * @inheritdoc
     */
    public function getDefaultShippingAddress($customerId)
    {
        //TODO: use cache MAGETWO-16862
        $customer = $this->_converter->getCustomerModel($customerId);
        $address = $customer->getDefaultShippingAddress();
        if ($address === false) {
            return null;
        }
        return $this->_createAddress($address,
            $customer->getDefaultBilling(),
            $customer->getDefaultShipping()
        );
    }

    /**
     * @inheritdoc
     */
    public function getAddressById($customerId, $addressId)
    {
        //TODO: use cache MAGETWO-16862
        $customer = $this->_converter->getCustomerModel($customerId);
        $address = $customer->getAddressById($addressId);
        if (!$address->getId()) {
            throw new Exception(
                'Address id ' . $addressId . ' not found',
                Exception::CODE_ADDRESS_NOT_FOUND
            );
        }
        return $this->_createAddress(
            $address,
            $customer->getDefaultBilling(),
            $customer->getDefaultShipping()
        );
    }

    /**
     * @inheritdoc
     */
    public function deleteAddressFromCustomer($customerId, $addressId)
    {
        if (!$addressId) {
            throw new Exception('Invalid addressId', Exception::CODE_INVALID_ADDRESS_ID);
        }

        $address = $this->_addressFactory->create();
        $address->load($addressId);

        if (!$address->getId()) {
            throw new Exception(
                'Address id ' . $addressId . ' not found',
                Exception::CODE_ADDRESS_NOT_FOUND
            );
        }

        // Validate address_id <=> customer_id
        if ($address->getCustomerId() != $customerId) {
            throw new Exception(
                'The address does not belong to this customer',
                Exception::CODE_CUSTOMER_ID_MISMATCH
            );
        }

        $address->delete();
    }

    /**
     * @inheritdoc
     */
    public function saveAddresses($customerId, array $addresses)
    {
        $customerModel = $this->_converter->getCustomerModel($customerId);
        $addressModels = [];

        $aggregateException = new AggregateException("All validation exceptions for all addresses.",
            Exception::CODE_VALIDATION_FAILED);
        foreach ($addresses as $address) {
            $addressModel = null;
            if ($address->getId()) {
                $addressModel = $customerModel->getAddressItemById($address->getId());
            }
            if (is_null($addressModel)) {
                $addressModel = $this->_addressFactory->create();
                $addressModel->setCustomer($customerModel);
            }
            $this->_updateAddressModel($addressModel, $address);

            $validationErrors = $addressModel->validate();
            if ($validationErrors !== true) {
                $aggregateException->pushException(
                    new Exception(
                        'There were one or more errors validating the address with id ' . $address->getId(),
                        Exception::CODE_VALIDATION_FAILED,
                        new \Magento\Validator\ValidatorException([$validationErrors])
                    )
                );
                continue;
            }
            $addressModels[] = $addressModel;
        }
        if ($aggregateException->hasExceptions()) {
            throw $aggregateException;
        }
        $addressIds = [];

        foreach ($addressModels as $addressModel) {
            try {
                $addressModel->save();
                $addressIds[] = $addressModel->getId();
            } catch (\Exception $e) {
                switch ($e->getCode()) {
                    case \Magento\Customer\Model\Customer::EXCEPTION_EMAIL_EXISTS:
                        $code = Exception::CODE_EMAIL_EXISTS;
                        break;
                    default:
                        $code = Exception::CODE_UNKNOWN;
                }
                throw new Exception($e->getMessage(), $code, $e);
            }
        }

        return $addressIds;
    }

    /**
     * Updates an Address Model based on information from an Address DTO.
     *
     * @param CustomerAddressModel $addressModel
     * @param Dto\Address $address
     * return null
     */
    private function _updateAddressModel(CustomerAddressModel $addressModel, Dto\Address $address)
    {
        // Set all attributes
        foreach ($address->getAttributes() as $attributeCode => $attributeData) {
            if ('region' == $attributeCode
                && $address->getRegion() instanceof Dto\Region
            ) {
                $addressModel->setData('region', $address->getRegion()->getRegion());
                $addressModel->setData('region_code', $address->getRegion()->getRegionCode());
                $addressModel->setData('region_id', $address->getRegion()->getRegionId());
            } else {
                $addressModel->setData($attributeCode, $attributeData);
            }
        }
        // Set customer related data
        $isBilling = $address->isDefaultBilling();
        $addressModel->setIsDefaultBilling($isBilling);
        $addressModel->setIsDefaultShipping($address->isDefaultShipping());
        // Need to use attribute set or future updates can cause data loss
        if (!$addressModel->getAttributeSetId()) {
            $addressModel->setAttributeSetId(CustomerMetadataServiceInterface::ADDRESS_ATTRIBUTE_SET_ID);
        }
    }

    /**
     * Create address based on model
     *
     * @param CustomerAddressModel $addressModel
     * @param int $defaultBillingId
     * @param int $defaultShippingId
     * @return Dto\Address
     */
    private function _createAddress(CustomerAddressModel $addressModel,
                                    $defaultBillingId, $defaultShippingId
    ) {
        $addressId = $addressModel->getId();
        $validAttributes = array_merge(
            $addressModel->getDefaultAttributeCodes(),
            [
                'id', 'region_id', 'region', 'street', 'vat_is_valid',
                'default_billing', 'default_shipping',
                //TODO: create VAT object at MAGETWO-16860
                'vat_request_id', 'vat_request_date', 'vat_request_success'
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
            'street' => $addressModel->getStreet(),
            'id' => $addressId,
            'default_billing' => $addressId === $defaultBillingId,
            'default_shipping' => $addressId === $defaultShippingId,
            'customer_id' => $addressModel->getCustomerId(),
            'region' => $region
        ]));

        $retValue = $this->_addressBuilder->create();
        return $retValue;
    }
}

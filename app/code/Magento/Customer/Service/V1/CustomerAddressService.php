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
use Magento\Customer\Model\Address\Converter as AddressConverter;

class CustomerAddressService implements CustomerAddressServiceInterface
{
    /** @var \Magento\Customer\Model\AddressFactory */
    private $_addressFactory;

    /**
     * @var \Magento\Customer\Model\Converter
     */
    private $_converter;

    /**
     * @var AddressConverter
     */
    private $_addressConverter;

    /**
     * Constructor
     *
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Customer\Model\Converter $converter
     * @param AddressConverter $addressConverter
     */
    public function __construct(
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\Converter $converter,
        AddressConverter $addressConverter
    ) {
        $this->_addressFactory = $addressFactory;
        $this->_converter = $converter;
        $this->_addressConverter = $addressConverter;
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
        /** @var $address \Magento\Customer\Model\Address */
        foreach ($addresses as $address) {
            $result[] = $this->_addressConverter->createAddressFromModel(
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
        return $this->_addressConverter->createAddressFromModel(
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
        return $this->_addressConverter->createAddressFromModel($address,
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
        return $this->_addressConverter->createAddressFromModel(
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
            $this->_addressConverter->updateAddressModel($addressModel, $address);

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
}

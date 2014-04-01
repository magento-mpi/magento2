<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1;

use Magento\Customer\Model\Address as CustomerAddressModel;
use Magento\Customer\Model\Address\Converter as AddressConverter;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Exception\NoSuchEntityException;
use Magento\Exception\InputException;

/**
 * Service related to Customer Address related functions
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomerAddressService implements CustomerAddressServiceInterface
{
    /**
     * @var \Magento\Customer\Model\Converter
     */
    private $converter;

    /**
     * @var AddressConverter
     */
    private $addressConverter;

    /**
     * Directory data
     *
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryData;

    /**
     * @var \Magento\Customer\Model\AddressRegistry
     */
    protected $addressRegistry;

    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * Constructor
     *
     * @param \Magento\Customer\Model\Converter $converter
     * @param \Magento\Customer\Model\AddressRegistry $addressRegistry
     * @param AddressConverter $addressConverter
     * @param \Magento\Directory\Helper\Data $directoryData
     */
    public function __construct(
        \Magento\Customer\Model\Converter $converter,
        \Magento\Customer\Model\AddressRegistry $addressRegistry,
        AddressConverter $addressConverter,
        CustomerRegistry $customerRegistry,
        \Magento\Directory\Helper\Data $directoryData
    ) {
        $this->converter = $converter;
        $this->addressRegistry = $addressRegistry;
        $this->addressConverter = $addressConverter;
        $this->customerRegistry = $customerRegistry;
        $this->directoryData = $directoryData;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddresses($customerId)
    {
        $customer = $this->customerRegistry->retrieve($customerId);
        $addresses = $customer->getAddresses();
        $defaultBillingId = $customer->getDefaultBilling();
        $defaultShippingId = $customer->getDefaultShipping();

        $result = array();
        /** @var $address CustomerAddressModel */
        foreach ($addresses as $address) {
            $result[] = $this->addressConverter->createAddressFromModel(
                $address,
                $defaultBillingId,
                $defaultShippingId
            );
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultBillingAddress($customerId)
    {
        $customer = $this->customerRegistry->retrieve($customerId);
        $address = $customer->getDefaultBillingAddress();
        if ($address === false) {
            return null;
        }
        return $this->addressConverter->createAddressFromModel(
            $address,
            $customer->getDefaultBilling(),
            $customer->getDefaultShipping()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultShippingAddress($customerId)
    {
        $customer = $this->customerRegistry->retrieve($customerId);
        $address = $customer->getDefaultShippingAddress();
        if ($address === false) {
            return null;
        }
        return $this->addressConverter->createAddressFromModel($address,
            $customer->getDefaultBilling(),
            $customer->getDefaultShipping()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress($addressId)
    {
        $address = $this->addressRegistry->retrieve($addressId);
        $customer = $this->customerRegistry->retrieve($address->getCustomerId());

        return $this->addressConverter->createAddressFromModel(
            $address,
            $customer->getDefaultBilling(),
            $customer->getDefaultShipping()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAddress($addressId)
    {
        $address = $this->addressRegistry->retrieve($addressId);
        $address->delete();
        $this->addressRegistry->remove($addressId);
    }

    /**
     * {@inheritdoc}
     */
    public function saveAddresses($customerId, $addresses)
    {
        $customerModel = $this->customerRegistry->retrieve($customerId);
        $addressModels = [];

        $inputException = new InputException();
        for ($i = 0; $i < count($addresses); $i++) {
            $address = $addresses[$i];
            $addressModel = null;
            if ($address->getId()) {
                $addressModel = $customerModel->getAddressItemById($address->getId());
            }

            if (is_null($addressModel)) {
                $addressModel = $this->addressConverter->createAddressModel($address);
                $addressModel->setCustomer($customerModel);
            } else {
                $this->addressConverter->updateAddressModel($addressModel, $address);
            }

            $inputException = $this->_validate($addressModel, $inputException, $i);
            $addressModels[] = $addressModel;
        }
        if ($inputException->getErrors()) {
            $this->customerRegistry->remove($customerId);
            throw $inputException;
        }
        $addressIds = [];

        /** @var \Magento\Customer\Model\Address $addressModel */
        foreach ($addressModels as $addressModel) {
            $addressModel->save();
            $this->addressRegistry->remove($addressModel->getId());
            $addressIds[] = $addressModel->getId();
        }

        $this->customerRegistry->remove($customerId);
        return $addressIds;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAddresses($addresses)
    {
        $inputException = new InputException();
        foreach ($addresses as $key => $address) {
            $addressModel = $this->addressConverter->createAddressModel($address);
            $inputException = $this->_validate($addressModel, $inputException, $key);
        }
        if ($inputException->getErrors()) {
            throw $inputException;
        }
        return true;
    }

    /**
     * Validate Customer Addresses attribute values.
     *
     * @param CustomerAddressModel $customerAddressModel the model to validate
     * @param InputException       $exception            the exception to add errors to
     * @param int                  $index                the index of the address being saved
     * @return InputException
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function _validate(CustomerAddressModel $customerAddressModel, InputException $exception, $index)
    {
        if ($customerAddressModel->getShouldIgnoreValidation()) {
            return $exception;
        }

        if (!\Zend_Validate::is($customerAddressModel->getFirstname(), 'NotEmpty')) {
            $exception->addError(
                InputException::REQUIRED_FIELD,
                'firstname',
                null,
                ['index' => $index]
            );
        }

        if (!\Zend_Validate::is($customerAddressModel->getLastname(), 'NotEmpty')) {
            $exception->addError(
                InputException::REQUIRED_FIELD,
                'lastname',
                null,
                ['index' => $index]
            );
        }

        if (!\Zend_Validate::is($customerAddressModel->getStreet(1), 'NotEmpty')) {
            $exception->addError(
                InputException::REQUIRED_FIELD,
                'street',
                null,
                ['index' => $index]
            );
        }

        if (!\Zend_Validate::is($customerAddressModel->getCity(), 'NotEmpty')) {
            $exception->addError(
                InputException::REQUIRED_FIELD,
                'city',
                null,
                ['index' => $index]
            );
        }

        if (!\Zend_Validate::is($customerAddressModel->getTelephone(), 'NotEmpty')) {
            $exception->addError(
                InputException::REQUIRED_FIELD,
                'telephone',
                null,
                ['index' => $index]
            );
        }

        $_havingOptionalZip = $this->directoryData->getCountriesWithOptionalZip();
        if (!in_array($customerAddressModel->getCountryId(), $_havingOptionalZip)
            && !\Zend_Validate::is($customerAddressModel->getPostcode(), 'NotEmpty')
        ) {
            $exception->addError(
                InputException::REQUIRED_FIELD,
                'postcode',
                null,
                ['index' => $index]
            );
        }

        if (!\Zend_Validate::is($customerAddressModel->getCountryId(), 'NotEmpty')) {
            $exception->addError(
                InputException::REQUIRED_FIELD,
                'countryId',
                null,
                ['index' => $index]
            );
        }

        if ($customerAddressModel->getCountryModel()->getRegionCollection()->getSize()
            && !\Zend_Validate::is($customerAddressModel->getRegionId(), 'NotEmpty')
            && $this->directoryData->isRegionRequired($customerAddressModel->getCountryId())
        ) {
            $exception->addError(
                InputException::REQUIRED_FIELD,
                'regionId',
                null,
                ['index' => $index]
            );
        }

        return $exception;
    }
}

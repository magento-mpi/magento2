<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

use Magento\Customer\Model\Converter;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Exception\InputException;
use Magento\Exception\NoSuchEntityException;
use Magento\Validator\ValidatorException;

/**
 * Manipulate Customer Address Entities *
 */
class CustomerService implements CustomerServiceInterface
{

    /** @var array Cache of DTOs */
    private $_cache = [];

    /**
     * @var Converter
     */
    private $_converter;

    /**
     * @var CustomerMetadataService
     */
    private $_customerMetadataService;


    /**
     * Constructor
     *
     * @param Converter $converter
     * @param CustomerMetadataService $customerMetadataService
     */
    public function __construct(
        Converter $converter,
        CustomerMetadataService $customerMetadataService
    ) {
        $this->_converter = $converter;
        $this->_customerMetadataService = $customerMetadataService;
    }


    /**
     * {@inheritdoc}
     */
    public function getCustomer($customerId)
    {
        if (!isset($this->_cache[$customerId])) {
            $customerModel = $this->_converter->getCustomerModel($customerId);
            $customerEntity = $this->_converter->createCustomerFromModel($customerModel);
            $this->_cache[$customerId] = $customerEntity;
        }

        return $this->_cache[$customerId];
    }


    /**
     * {@inheritdoc}
     */
    public function saveCustomer(Dto\Customer $customer, $password = null)
    {
        $customerModel = $this->_converter->createCustomerModel($customer);

        if ($password) {
            $customerModel->setPassword($password);
        }

        $this->_validate($customerModel);

        $customerModel->save();
        unset($this->_cache[$customerModel->getId()]);

        return $customerModel->getId();
    }

    /**
     * Validate customer attribute values.
     *
     * @param CustomerModel $customerModel
     * @throws InputException
     * @return void
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function _validate(CustomerModel $customerModel)
    {
        $exception = new InputException();
        if (!\Zend_Validate::is(trim($customerModel->getFirstname()), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, 'firstname', '');
        }

        if (!\Zend_Validate::is(trim($customerModel->getLastname()), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, 'lastname', '');
        }

        if (!\Zend_Validate::is($customerModel->getEmail(), 'EmailAddress')) {
            $exception->addError(InputException::INVALID_FIELD_VALUE, 'email', $customerModel->getEmail());
        }

        try {
            $dob = $this->_customerMetadataService->getCustomerAttributeMetadata('dob');
            if ($dob->isRequired() && '' == trim($customerModel->getDob())) {
                $exception->addError(InputException::REQUIRED_FIELD, 'dob', '');
            }
        } catch (NoSuchEntityException $e) {
            // skip
        }
        try {
            $taxvat = $this->_customerMetadataService->getCustomerAttributeMetadata('taxvat');
            if ($taxvat->isRequired() && '' == trim($customerModel->getTaxvat())) {
                $exception->addError(InputException::REQUIRED_FIELD, 'taxvat', '');
            }
        } catch (NoSuchEntityException $e) {
            // skip
        }
        try {
            $gender = $this->_customerMetadataService->getCustomerAttributeMetadata('gender');
            if ($gender->isRequired() && '' == trim($customerModel->getGender())) {
                $exception->addError(InputException::REQUIRED_FIELD, 'gender', '');
            }
        } catch (NoSuchEntityException $e) {
            // skip
        }
        if ($exception->getErrors()) {
            throw $exception;
        }
    }
}

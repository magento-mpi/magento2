<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

use Magento\Customer\Model\Converter;
use Magento\Customer\Service\Entity\V1\Exception;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Exception\InputException;
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
     * @inheritdoc
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
     * @inheritdoc
     */
    public function saveCustomer(Dto\Customer $customer, $password = null)
    {
        $customerModel = $this->_converter->createCustomerModel($customer);

        if ($password) {
            $customerModel->setPassword($password);
        }

        $this->_validate($customerModel);

        try {
            $customerModel->save();
            unset($this->_cache[$customerModel->getId()]);
        } catch (\Magento\Customer\Exception $e) {
            switch ($e->getCode()) {
                case CustomerModel::EXCEPTION_EMAIL_EXISTS:
                    throw InputException::create('email', InputException::DUPLICATE_UNIQUE_VALUE_EXISTS);
                default:
                    throw $e;
            }
        }

        return $customerModel->getId();
    }


    /**
     * Validate customer attribute values.
     *
     * @param CustomerModel $customerModel
     * @throws InputException
     * @return void
     */
    private function _validate(CustomerModel $customerModel)
    {
        $exception = new InputException();
        if (!\Zend_Validate::is(trim($customerModel->getFirstname()), 'NotEmpty')) {
            $exception->addError('firstname', InputException::EMPTY_FIELD_REQUIRED);
        }

        if (!\Zend_Validate::is(trim($customerModel->getLastname()), 'NotEmpty')) {
            $exception->addError('lastname', InputException::EMPTY_FIELD_REQUIRED);
        }

        if (!\Zend_Validate::is($customerModel->getEmail(), 'EmailAddress')) {
            $exception->addError('email', InputException::INVALID_FIELD_VALUE, ['value' => $customerModel->getEmail()]);
        }

        $dob = $this->_customerMetadataService->getCustomerAttributeMetadata('dob');
        if ($dob->getIsRequired() && '' == trim($customerModel->getDob())) {
            $exception->addError('dob', InputException::EMPTY_FIELD_REQUIRED);
        }
        $taxvat = $this->_customerMetadataService->getCustomerAttributeMetadata('taxvat');
        if ($taxvat->getIsRequired() && '' == trim($customerModel->getTaxvat())) {
            $exception->addError('taxvat', InputException::EMPTY_FIELD_REQUIRED);
        }
        $gender = $this->_customerMetadataService->getCustomerAttributeMetadata('gender');
        if ($gender->getIsRequired() && '' == trim($customerModel->getGender())) {
            $exception->addError('gender', InputException::EMPTY_FIELD_REQUIRED);
        }
        if ($exception->getParams()) {
            throw $exception;
        }
    }
}

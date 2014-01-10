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
     * Constructor
     *
     * @param Converter $converter
     */
    public function __construct(
        Converter $converter
    ) {
        $this->_converter = $converter;
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

        $validationErrors = $customerModel->validate();
        if ($validationErrors !== true) {
            throw new Exception(
                'There were one or more errors validating the customer object.',
                Exception::CODE_VALIDATION_FAILED,
                new ValidatorException([$validationErrors])
            );
        }

        try {
            $customerModel->save();
            unset($this->_cache[$customerModel->getId()]);
        } catch (\Exception $e) {
            switch ($e->getCode()) {
                case CustomerModel::EXCEPTION_EMAIL_EXISTS:
                    $code = Exception::CODE_EMAIL_EXISTS;
                    break;
                default:
                    $code = Exception::CODE_UNKNOWN;
            }
            throw new Exception($e->getMessage(), $code, $e);
        }

        return $customerModel->getId();
    }
}

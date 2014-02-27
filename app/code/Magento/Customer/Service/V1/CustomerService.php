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
     * {@inheritdoc}
     */
    public function getCustomer($customerId)
    {
        $customerModel = $this->_converter->getCustomerModel($customerId);
        return $this->_converter->createCustomerFromModel($customerModel);
    }


    /**
     * {@inheritdoc}
     */
    public function getCustomerByEmail($customerEmail, $websiteId = null)
    {
        $customerModel = $this->_converter->getCustomerModelByEmail($customerEmail, $websiteId);
        return $this->_converter->createCustomerFromModel($customerModel);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCustomer($customerId)
    {
        $customerModel = $this->_converter->getCustomerModel($customerId);
        $customerModel->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function isReadonly($customerId)
    {
        $customerModel = $this->_converter->getCustomerModel($customerId);
        return $customerModel->isReadonly();
    }

    /**
     * {@inheritdoc}
     */
    public function isDeleteable($customerId)
    {
        $customerModel = $this->_converter->getCustomerModel($customerId);
        return $customerModel->isDeleteable();
    }
}

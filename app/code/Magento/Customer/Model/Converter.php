<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model;

use Magento\Customer\Service\V1\CustomerMetadataServiceInterface;
use Magento\Exception\NoSuchEntityException;
use Magento\Customer\Service\V1\Dto\Customer as CustomerDto;
use Magento\Customer\Service\V1\Dto\CustomerBuilder as CustomerDtoBuilder;

/**
 * Customer Model converter.
 *
 * Converts a Customer Model to a DTO or vice versa.
 */
class Converter
{
    /**
     * @var CustomerDtoBuilder
     */
    protected $_customerBuilder;

    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @param CustomerDtoBuilder $customerBuilder
     * @param CustomerFactory $customerFactory
     */
    public function __construct(CustomerDtoBuilder $customerBuilder, CustomerFactory $customerFactory)
    {
        $this->_customerBuilder = $customerBuilder;
        $this->_customerFactory = $customerFactory;
    }

    /**
     * Convert a customer model to a customer entity
     *
     * @param Customer $customerModel
     * @return CustomerDto
     */
    public function createCustomerFromModel(Customer $customerModel)
    {
        $customerBuilder = $this->_populateBuilderWithAttributes($customerModel);
        $customerBuilder->setCustomerId($customerModel->getId());
        $customerBuilder->setFirstname($customerModel->getFirstname());
        $customerBuilder->setLastname($customerModel->getLastname());
        $customerBuilder->setEmail($customerModel->getEmail());
        return $customerBuilder->create();
    }


    /**
     * Retrieve customer model by his ID.
     *
     * @param int $customerId
     * @return Customer
     * @throws NoSuchEntityException If customer with customerId is not found.
     */
    public function getCustomerModel($customerId)
    {
        $customer = $this->_customerFactory->create()->load($customerId);

        if (!$customer->getId()) {
            // customer does not exist
            throw new NoSuchEntityException('customerId', $customerId);
        } else {
            return $customer;
        }
    }

    /**
     * Retrieve customer model by his email.
     *
     * @param string $customerEmail
     * @param int $websiteId
     * @throws NoSuchEntityException If customer with the specified customer email not found.
     * @return Customer
     */
    public function getCustomerModelByEmail($customerEmail, $websiteId = null)
    {
        $customer = $this->_customerFactory->create();
        if (isset($websiteId)) {
            $customer->setWebsiteId($websiteId);
        }
        $customer->loadByEmail($customerEmail);
        if (!$customer->getId()) {
            throw new NoSuchEntityException('email', $customerEmail);
        } else {
            return $customer;
        }
    }

    /**
     * Creates a customer model from a customer entity.
     *
     * @param CustomerDto $customer
     * @return Customer
     */
    public function createCustomerModel(CustomerDto $customer)
    {
        $customerModel = $this->_customerFactory->create();

        $attributes = $customer->getAttributes();
        foreach ($attributes as $attributeCode => $attributeValue) {
            // avoid setting password through set attribute
            if ($attributeCode == 'password') {
                continue;
            } else {
                $customerModel->setData($attributeCode, $attributeValue);
            }
        }

        $customerId = $customer->getCustomerId();
        if ($customerId) {
            $customerModel->setId($customerId);
        }

        // Need to use attribute set or future updates can cause data loss
        if (!$customerModel->getAttributeSetId()) {
            $customerModel->setAttributeSetId(CustomerMetadataServiceInterface::ATTRIBUTE_SET_ID_CUSTOMER);
        }

        return $customerModel;
    }

    /**
     * Update customer model with the data from the data object
     *
     * @param Customer $customerModel
     * @param \Magento\Customer\Service\V1\Dto\Customer $customerData
     * @return void
     */
    public function updateCustomerModel(
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Customer\Service\V1\Dto\Customer $customerData
    ) {
        $attributes = $customerData->__toArray();
        foreach ($attributes as $attributeCode => $attributeValue) {
            $customerModel->setDataUsingMethod($attributeCode, $attributeValue);
        }
        $customerId = $customerData->getCustomerId();
        if ($customerId) {
            $customerModel->setId($customerId);
        }
        // Need to use attribute set or future calls to customerModel::save can cause data loss
        if (!$customerModel->getAttributeSetId()) {
            $customerModel->setAttributeSetId(CustomerMetadataServiceInterface::ATTRIBUTE_SET_ID_CUSTOMER);
        }
    }

    /**
     * Loads the values from a customer model
     *
     * @param Customer $customerModel
     * @return CustomerDtoBuilder
     */
    protected function _populateBuilderWithAttributes(Customer $customerModel)
    {
        $attributes = [];
        $systemAttributes = ['entity_type_id', 'attribute_set_id'];
        foreach ($customerModel->getAttributes() as $attribute) {
            $attrCode = $attribute->getAttributeCode();
            $value = $customerModel->getDataUsingMethod($attrCode);
            if (null === $value || in_array($attrCode, $systemAttributes)) {
                continue;
            }
            if ($attrCode == 'entity_id') {
                $attributes[\Magento\Customer\Service\V1\Dto\Customer::ID] = $value;
            } else {
                $attributes[$attrCode] = $value;
            }
        }

        return $this->_customerBuilder->populateWithArray($attributes);
    }

}

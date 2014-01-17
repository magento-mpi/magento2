<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model;

use Magento\Customer\Service\V1\CustomerMetadataServiceInterface;
use Magento\Exception\NoSuchEntityException;

/**
 * Customer Model converter.
 *
 * Converts a Customer Model to a DTO.
 *
 * TODO: Remove this class after service refactoring is done and the model
 * TODO: is no longer needed outside of service.  Then this function could
 * TODO: be moved to the service.
 */
class Converter
{
    /**
     * @var \Magento\Customer\Service\V1\Dto\CustomerBuilder
     */
    protected $_customerBuilder;

    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @param CustomerFactory $customerFactory
     * @param \Magento\Customer\Service\V1\Dto\CustomerBuilder $customerBuilder
     */
    public function __construct(
        \Magento\Customer\Service\V1\Dto\CustomerBuilder $customerBuilder,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->_customerBuilder = $customerBuilder;
        $this->_customerFactory = $customerFactory;
    }

    /**
     * Convert a customer model to a customer entity
     *
     * @param \Magento\Customer\Model\Customer $customerModel
     * @throws \InvalidArgumentException
     * @return \Magento\Customer\Service\V1\Dto\Customer
     */
    public function createCustomerFromModel($customerModel)
    {
        if (!($customerModel instanceof \Magento\Customer\Model\Customer)) {
            throw new \InvalidArgumentException('customer model is invalid');
        }
        $this->_convertAttributesFromModel($this->_customerBuilder, $customerModel);
        $this->_customerBuilder->setCustomerId($customerModel->getId());
        $this->_customerBuilder->setFirstname($customerModel->getFirstname());
        $this->_customerBuilder->setLastname($customerModel->getLastname());
        $this->_customerBuilder->setEmail($customerModel->getEmail());
        return $this->_customerBuilder->create();
    }


    /**
     * @param int $customerId
     * @throws NoSuchEntityException If customer with customerId is not found.
     * @return Customer
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
     * Creates a customer model from a customer entity.
     *
     * @param \Magento\Customer\Service\V1\Dto\Customer $customer
     * @return Customer
     */
    public function createCustomerModel(\Magento\Customer\Service\V1\Dto\Customer $customer)
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
            $customerModel->setAttributeSetId(CustomerMetadataServiceInterface::CUSTOMER_ATTRIBUTE_SET_ID);
            return $customerModel;
        }

        return $customerModel;
    }

    /**
     * Loads the values from a customer model
     *
     * @param \Magento\Customer\Service\V1\Dto\CustomerBuilder $customerBuilder
     * @param \Magento\Customer\Model\Customer $customerModel
     */
    protected function _convertAttributesFromModel($customerBuilder, $customerModel)
    {
        $attributes = [];
        foreach ($customerModel->getAttributes() as $attribute) {
            $attrCode = $attribute->getAttributeCode();
            $value = $customerModel->getData($attrCode);
            if (null == $value) {
                continue;
            }
            $attributes[$attrCode] = $value;
        }

        $customerBuilder->populateWithArray($attributes);
    }

}

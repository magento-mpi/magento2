<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Api\Data\CustomerInterface as CustomerDataObject;
use Magento\Customer\Api\Data\CustomerDataBuilder as CustomerDataObjectBuilder;
use Magento\Framework\StoreManagerInterface;

/**
 * Customer Model converter.
 *
 * Converts a Customer Model to a Data Object or vice versa.
 */
class Converter
{
    /**
     * @var CustomerDataObjectBuilder
     */
    protected $_customerBuilder;

    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @param CustomerDataObjectBuilder $customerBuilder
     * @param CustomerFactory $customerFactory
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        CustomerDataObjectBuilder $customerBuilder,
        CustomerFactory $customerFactory,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->_customerBuilder = $customerBuilder;
        $this->_customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * Retrieve customer model by his ID if possible, or return an empty model otherwise.
     *
     * @param int $customerId
     * @return Customer
     */
    public function loadCustomerModel($customerId)
    {
        return $this->_customerFactory->create()->load($customerId);
    }

    /**
     * Retrieve customer model by his email.
     *
     * @param string $customerEmail
     * @param int $websiteId
     * @throws NoSuchEntityException If customer with the specified customer email not found.
     * @throws \Magento\Framework\Model\Exception If website was not specified
     * @return Customer
     */
    public function getCustomerModelByEmail($customerEmail, $websiteId = null)
    {
        $customer = $this->_customerFactory->create();
        if (!isset($websiteId)) {
            $websiteId = $this->storeManager->getDefaultStoreView()->getWebsiteId();
        }
        $customer->setWebsiteId($websiteId);

        $customer->loadByEmail($customerEmail);
        if (!$customer->getId()) {
            throw new NoSuchEntityException(
                NoSuchEntityException::MESSAGE_SINGLE_FIELD,
                ['fieldName' => 'email', 'fieldValue' => $customerEmail]
            );
        } else {
            return $customer;
        }
    }

    /**
     * Creates a customer model from a customer entity.
     *
     * @param CustomerDataObject $customer
     * @return Customer
     */
    public function createCustomerModel(CustomerDataObject $customer)
    {
        $customerModel = $this->_customerFactory->create();

        $attributes = $this->extensibleDataObjectConverter->toFlatArray($customer);
        foreach ($attributes as $attributeCode => $attributeValue) {
            // avoid setting password through set attribute
            if ($attributeCode == 'password') {
                continue;
            } else {
                $customerModel->setData($attributeCode, $attributeValue);
            }
        }

        $customerId = $customer->getId();
        if ($customerId) {
            $customerModel->setId($customerId);
        }

        // Need to use attribute set or future updates can cause data loss
        if (!$customerModel->getAttributeSetId()) {
            $customerModel->setAttributeSetId(
                \Magento\Customer\Api\CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER
            );
        }

        return $customerModel;
    }

    /**
     * Update customer model with the data from the data object
     *
     * @param Customer $customerModel
     * @param CustomerDataObject $customerData
     * @return void
     */
    public function updateCustomerModel(
        \Magento\Customer\Model\Customer $customerModel,
        CustomerDataObject $customerData
    ) {
        $attributes = $this->extensibleDataObjectConverter->toFlatArray($customerData);
        foreach ($attributes as $attributeCode => $attributeValue) {
            $customerModel->setDataUsingMethod($attributeCode, $attributeValue);
        }
        $customerId = $customerData->getId();
        if ($customerId) {
            $customerModel->setId($customerId);
        }
        // Need to use attribute set or future calls to customerModel::save can cause data loss
        if (!$customerModel->getAttributeSetId()) {
            $customerModel->setAttributeSetId(
                \Magento\Customer\Api\CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER
            );
        }
    }

    /**
     * Loads the values from a customer model
     *
     * @param Customer $customerModel
     * @return CustomerDataObjectBuilder
     */
    protected function _populateBuilderWithAttributes(Customer $customerModel)
    {
        $attributes = array();
        foreach ($customerModel->getAttributes() as $attribute) {
            $attrCode = $attribute->getAttributeCode();
            $value = $customerModel->getDataUsingMethod($attrCode);
            $value = $value ? $value : $customerModel->getData($attrCode);
            if (null !== $value) {
                if ($attrCode == 'entity_id') {
                    $attributes[\Magento\Customer\Model\Data\Customer::ID] = $value;
                } else {
                    $attributes[$attrCode] = $value;
                }
            }
        }

        return $this->_customerBuilder->populateWithArray($attributes);
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Resource;

use Magento\Customer\Model\Address as CustomerAddressModel;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\Data\SearchCriteriaInterface;

/**
 * Customer repository.
 */
class CustomerRepository implements \Magento\Customer\Api\CustomerRepositoryInterface
{
    /**
     * @var \Magento\Webapi\Model\DataObjectProcessor
     */
    protected $dataProcessor;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var \Magento\Customer\Model\Resource\AddressRepository
     */
    protected $addressRepository;

    /**
     * @var \Magento\Customer\Model\Resource\Customer
     */
    protected $customerResourceModel;

    /**
     * @var \Magento\Customer\Api\CustomerMetadataInterface
     */
    protected $customerMetadata;

    /**
     * @var \Magento\Customer\Api\Data\AddressDataBuilder
     */
    protected $addressBuilder;

    /**
     * @var \Magento\Customer\Api\Data\CustomerDataBuilder
     */
    protected $customerBuilder;

    /**
     * @var \Magento\Customer\Api\Data\CustomerSearchResultsDataBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Webapi\Model\DataObjectProcessor $dataProcessor
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Magento\Customer\Model\Resource\AddressRepository $addressRepository
     * @param \Magento\Customer\Model\Resource\Customer $customerResourceModel
     * @param \Magento\Customer\Api\CustomerMetadataInterface $customerMetadata
     * @param \Magento\Customer\Api\Data\AddressDataBuilder $addressBuilder
     * @param \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder
     * @param \Magento\Customer\Api\Data\CustomerSearchResultsDataBuilder $searchResultsDataBuilder
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Webapi\Model\DataObjectProcessor $dataProcessor,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Customer\Model\Resource\AddressRepository $addressRepository,
        \Magento\Customer\Model\Resource\Customer $customerResourceModel,
        \Magento\Customer\Api\CustomerMetadataInterface $customerMetadata,
        \Magento\Customer\Api\Data\AddressDataBuilder $addressBuilder,
        \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder,
        \Magento\Customer\Api\Data\CustomerSearchResultsDataBuilder $searchResultsDataBuilder,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\StoreManagerInterface $storeManager
    ) {
        $this->dataProcessor = $dataProcessor;
        $this->customerFactory = $customerFactory;
        $this->customerRegistry = $customerRegistry;
        $this->addressRepository = $addressRepository;
        $this->customerResourceModel = $customerResourceModel;
        $this->customerMetadata = $customerMetadata;
        $this->addressBuilder = $addressBuilder;
        $this->customerBuilder = $customerBuilder;
        $this->searchResultsBuilder = $searchResultsDataBuilder;
        $this->eventManager = $eventManager;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        $isNewCustomer = $customer->getId() ? false : true;
        $this->validate($customer);
        $customerModel = $this->customerFactory->create(
            [
                'data' => $this->dataProcessor->buildOutputDataArray(
                    $customer,
                    'Magento\Customer\Api\Data\CustomerInterface'
                )
            ]
        );
        $storeId = $customerModel->getStoreId();
        if ($storeId === null) {
            $customerModel->setStoreId($this->storeManager->getStore()->getId());
        }
        $customerModel->getGroupId();
        $customerModel->setId($customer->getId());
        /** Prevent addresses being processed by resource model */
        $customerModel->unsAddresses();
        $this->customerResourceModel->save($customerModel);
        $this->customerRegistry->push($customerModel);
        $customerId = $customerModel->getId();
        foreach ($customer->getAddresses() as $address) {
            if ($isNewCustomer) {
                $address = $this->addressBuilder->populate($address)->setCustomerId($customerId)->create();
            }
            $this->addressRepository->save($address);
        }
        $savedCustomer = $this->get($customer->getEmail(), $customer->getWebsiteId());
        $this->eventManager->dispatch(
            'customer_save_after_data_object',
            ['customer_data_object' => $savedCustomer, 'orig_customer_data_object' => $customer]
        );
        return $savedCustomer;
    }

    /**
     * {@inheritdoc}
     */
    public function get($email, $websiteId = null)
    {
        $customerModel = $this->customerRegistry->retrieveByEmail($email, $websiteId);
        return $customerModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $this->searchResultsBuilder->setSearchCriteria($searchCriteria);
        /** @var \Magento\Customer\Model\Resource\Customer\Collection $collection */
        $collection = $this->customerFactory->create()->getCollection();
        // This is needed to make sure all the attributes are properly loaded
        foreach ($this->customerMetadata->getAllAttributesMetadata() as $metadata) {
            $collection->addAttributeToSelect($metadata->getAttributeCode());
        }
        // Needed to enable filtering on name as a whole
        $collection->addNameToSelect();
        // Needed to enable filtering based on billing address attributes
        $collection->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left')
            ->joinAttribute('company', 'customer_address/company', 'default_billing', null, 'left');
        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $this->searchResultsBuilder->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SearchCriteriaInterface::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $customers = [];
        /** @var \Magento\Customer\Model\Customer $customerModel */
        foreach ($collection as $customerModel) {
            $customers[] = $customerModel->getDataModel();
        }
        $this->searchResultsBuilder->setItems($customers);
        return $this->searchResultsBuilder->create();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        return $this->deleteById($customer->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($customerId)
    {
        $customerModel = $this->customerRegistry->retrieve($customerId);
        $customerModel->delete();
        $this->customerRegistry->remove($customerId);
        return true;
    }

    /**
     * Validate customer attribute values.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @throws InputException
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function validate(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        $exception = new InputException();
        if (!\Zend_Validate::is(trim($customer->getFirstname()), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'firstname']);
        }

        if (!\Zend_Validate::is(trim($customer->getLastname()), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'lastname']);
        }

        $isEmailAddress = \Zend_Validate::is(
            $customer->getEmail(),
            'EmailAddress',
            ['allow' => ['allow'=> \Zend_Validate_Hostname::ALLOW_ALL, 'tld' => false]]
        );

        if (!$isEmailAddress) {
            $exception->addError(
                InputException::INVALID_FIELD_VALUE,
                ['fieldName' => 'email', 'value' => $customer->getEmail()]
            );
        }

        $dob = $this->getAttributeMetadata('dob');
        if (!is_null($dob) && $dob->isRequired() && '' == trim($customer->getDob())) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'dob']);
        }

        $taxvat = $this->getAttributeMetadata('taxvat');
        if (!is_null($taxvat) && $taxvat->isRequired() && '' == trim($customer->getTaxvat())) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'taxvat']);
        }

        $gender = $this->getAttributeMetadata('gender');
        if (!is_null($gender) && $gender->isRequired() && '' == trim($customer->getGender())) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'gender']);
        }

        if ($exception->wasErrorAdded()) {
            throw $exception;
        }
    }

    /**
     * Get attribute metadata.
     *
     * @param string $attributeCode
     * @return \Magento\Customer\Api\Data\AttributeMetadataInterface|null
     */
    private function getAttributeMetadata($attributeCode)
    {
        try {
            return $this->customerMetadata->getAttributeMetadata($attributeCode);
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Service\V1\Data\Search\FilterGroup $filterGroup
     * @param \Magento\Customer\Model\Resource\Customer\Collection $collection
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(
        \Magento\Framework\Service\V1\Data\Search\FilterGroup $filterGroup,
        \Magento\Customer\Model\Resource\Customer\Collection $collection
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = array('attribute' => $filter->getField(), $condition => $filter->getValue());
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }
}

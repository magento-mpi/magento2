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
     * @param \Magento\Webapi\Model\DataObjectProcessor $dataProcessor
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Magento\Customer\Model\Resource\AddressRepository $addressRepository
     * @param \Magento\Customer\Model\Resource\Customer $customerResourceModel
     * @param \Magento\Customer\Api\CustomerMetadataInterface $customerMetadata
     * @param \Magento\Customer\Api\Data\AddressDataBuilder $addressBuilder
     * @param \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder
     */
    public function __construct(
        \Magento\Webapi\Model\DataObjectProcessor $dataProcessor,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Customer\Model\Resource\AddressRepository $addressRepository,
        \Magento\Customer\Model\Resource\Customer $customerResourceModel,
        \Magento\Customer\Api\CustomerMetadataInterface $customerMetadata,
        \Magento\Customer\Api\Data\AddressDataBuilder $addressBuilder,
        \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder
    ) {
        $this->dataProcessor = $dataProcessor;
        $this->customerFactory = $customerFactory;
        $this->customerRegistry = $customerRegistry;
        $this->addressRepository = $addressRepository;
        $this->customerResourceModel = $customerResourceModel;
        $this->customerMetadata = $customerMetadata;
        $this->addressBuilder = $addressBuilder;
        $this->customerBuilder = $customerBuilder;
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
        /** Prevent addresses being processed by resource model */
        $customerModel->unsAddresses();
        $this->customerResourceModel->save($customerModel);
        $customerId = $customerModel->getId();
        /** Clear the customer from registry so that the updated one can be retrieved next time */
        $this->customerRegistry->remove($customerId);
        foreach ($customer->getAddresses() as $address) {
            if ($isNewCustomer) {
                $address = $this->addressBuilder->populate($address)->setCustomerId($customerId)->create();
            }
            $this->addressRepository->save($address);
        }
        return $this->get($customer->getEmail(), $customer->getWebsiteId());
    }

    /**
     * {@inheritdoc}
     */
    public function get($email, $websiteId = null)
    {
        $customerModel = $this->customerRegistry->retrieveByEmail($email, $websiteId);
        return $this->customerBuilder
            ->populateWithArray($customerModel->getData())
            ->setId($customerModel->getId())
            ->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\Data\SearchCriteriaInterface $searchCriteria)
    {
        // TODO: Implement getList() method.
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        // TODO: Implement delete() method.
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($customerId)
    {
        // TODO: Implement deleteById() method.
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
}

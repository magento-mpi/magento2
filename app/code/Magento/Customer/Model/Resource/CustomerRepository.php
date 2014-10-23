<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Resource;

use Magento\Customer\Model\Address as CustomerAddressModel;

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
     * @var \Magento\Framework\Api\Data\SearchCriteriaDataBuilder
     */
    protected $searchCriteriaDataBuilder;

    /**
     * @param \Magento\Webapi\Model\DataObjectProcessor $dataProcessor
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Magento\Customer\Model\Resource\AddressRepository $addressRepository
     * @param \Magento\Framework\Api\Data\SearchCriteriaDataBuilder $searchCriteriaDataBuilder
     */
    public function __construct(
        \Magento\Webapi\Model\DataObjectProcessor $dataProcessor,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Customer\Model\Resource\AddressRepository $addressRepository,
        \Magento\Framework\Api\Data\SearchCriteriaDataBuilder $searchCriteriaDataBuilder
    ) {
        $this->dataProcessor = $dataProcessor;
        $this->customerFactory = $customerFactory;
        $this->customerRegistry = $customerRegistry;
        $this->addressRepository = $addressRepository;
        $this->searchCriteriaDataBuilder = $searchCriteriaDataBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        $customerData = $this->dataProcessor->buildOutputDataArray(
            $customer,
            'Magento\Customer\Api\CustomerRepositoryInterface'
        );
        $customerModel = $this->customerFactory->create($customerData);
        // Shouldn't we be calling validateCustomerData/Details here?
//        $this->validate($customerModel);

        $customerModel->save();
        // Clear the customer from registry so that the updated one can be retrieved next time
        $this->customerRegistry->remove($customerModel->getId());

        foreach ($customer->getAddresses() as $address) {
            $this->addressRepository->save($address);
        }
        // If $address is null, no changes must made to the list of addresses
        // be careful $addresses != null would be true of $addresses is an empty array
        if ($addresses !== null) {
            $existingAddresses = $this->getL();
            /** @var Data\Address[] $deletedAddresses */
            $deletedAddresses = array_udiff(
                $existingAddresses,
                $addresses,
                function (Data\Address $existing, Data\Address $replacement) {
                    return $existing->getId() - $replacement->getId();
                }
            );

            // If $addresses is an empty array, all addresses are removed.
            // array_udiff would return the entire $existing array
            foreach ($deletedAddresses as $address) {
                $this->customerAddressService->deleteAddress($address->getId());
            }
            $this->customerAddressService->saveAddresses($customer->getId(), $addresses);
        }

        return true;
    }

//    /**
//     * Validate customer attribute values.
//     *
//     * @param CustomerModel $customerModel
//     * @throws InputException
//     * @return void
//     *
//     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
//     * @SuppressWarnings(PHPMD.NPathComplexity)
//     */
//    private function validate(CustomerModel $customerModel)
//    {
//        $exception = new InputException();
//        if (!\Zend_Validate::is(trim($customerModel->getFirstname()), 'NotEmpty')) {
//            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'firstname']);
//        }
//
//        if (!\Zend_Validate::is(trim($customerModel->getLastname()), 'NotEmpty')) {
//            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'lastname']);
//        }
//
//        $isEmailAddress = \Zend_Validate::is(
//            $customerModel->getEmail(),
//            'EmailAddress',
//            ['allow' => ['allow'=> \Zend_Validate_Hostname::ALLOW_ALL, 'tld' => false]]
//        );
//
//        if (!$isEmailAddress) {
//            $exception->addError(
//                InputException::INVALID_FIELD_VALUE,
//                ['fieldName' => 'email', 'value' => $customerModel->getEmail()]
//            );
//        }
//
//        $dob = $this->getAttributeMetadata('dob');
//        if (!is_null($dob) && $dob->isRequired() && '' == trim($customerModel->getDob())) {
//            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'dob']);
//        }
//
//        $taxvat = $this->getAttributeMetadata('taxvat');
//        if (!is_null($taxvat) && $taxvat->isRequired() && '' == trim($customerModel->getTaxvat())) {
//            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'taxvat']);
//        }
//
//        $gender = $this->getAttributeMetadata('gender');
//        if (!is_null($gender) && $gender->isRequired() && '' == trim($customerModel->getGender())) {
//            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'gender']);
//        }
//
//        if ($exception->wasErrorAdded()) {
//            throw $exception;
//        }
//    }
}

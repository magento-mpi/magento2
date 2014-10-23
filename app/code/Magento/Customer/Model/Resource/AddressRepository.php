<?php
/**
 * Customer address entity resource model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Resource;

use \Magento\Framework\Exception\InputException;
use Magento\Customer\Model\Address as CustomerAddressModel;

class AddressRepository implements \Magento\Customer\Api\AddressRepositoryInterface
{
    /**
     * Directory data
     *
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryData;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $addressFactory;

    /**
     * @var \Magento\Customer\Model\AddressRegistry
     */
    protected $addressRegistry;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var \Magento\Customer\Model\Resource\Address
     */
    protected $addressResourceModel;

    /**
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Customer\Model\AddressRegistry $addressRegistry
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Magento\Customer\Model\Resource\Address $addressResourceModel
     */
    public function __construct(
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\AddressRegistry $addressRegistry,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Customer\Model\Resource\Address $addressResourceModel,
        \Magento\Directory\Helper\Data $directoryData
    ) {
        $this->addressFactory = $addressFactory;
        $this->addressRegistry = $addressRegistry;
        $this->customerRegistry = $customerRegistry;
        $this->addressResource = $addressResourceModel;
        $this->directoryData = $directoryData;
    }

    /**
     * Save customer address.
     *
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @return \Magento\Customer\Api\Data\AddressInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magento\Customer\Api\Data\AddressInterface $address)
    {
        $customerModel = $this->customerRegistry->retrieve($address->getCustomerId());
        $addressModel = null;
        if ($address->getId()) {
            $addressModel = $customerModel->getAddressItemById($address->getId());
            // is there set address by id
        }
        if (is_null($addressModel)) {
            $addressModel = $this->addressFactory->create();
            $addressModel->updateData($address);
            $addressModel->setCustomer($customerModel);
        } else {
            $addressModel->updateData($address);
        }
        var_dump($addressModel->getData());
        $inputException = $this->_validate($addressModel);
        if ($inputException->wasErrorAdded()) {
            echo $inputException->getRawMessage();
            //throw $inputException;
        }
        $this->addressResource->save($addressModel); // move before/after save from model to resource model
        $this->addressRegistry->push($addressModel); // customer set by id
        return $addressModel->getDataModel(true, true); // remove parameters
    }

    /**
     * Retrieve customer address.
     *
     * @param int $addressId
     * @return \Magento\Customer\Api\Data\Address
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($addressId)
    {
        $address = $this->addressRegistry->retrieve($addressId);
        $address->getDataModel(true, true);
    }

    /**
     * Retrieve customers addresses matching the specified criteria.
     *
     * @param \Magento\Framework\Api\Data\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Customer\Api\Data\AddressSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\Data\SearchCriteriaInterface $searchCriteria)
    {
        //return new \Magento\Customer\Api\Data\AddressSearchResultsInterface();
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $collection)
    {
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

    /**
     * Delete customer address.
     *
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magento\Customer\Api\Data\AddressInterface $address)
    {
        $address = $this->addressRegistry->retrieve($addressId);
        $address->delete();
        $address = $this->addressRegistry->remove($addressId);
        return true;
    }

    /**
     * Delete customer address by ID.
     *
     * @param int $addressId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($addressId)
    {

    }

    /**
     * Validate Customer Addresses attribute values.
     *
     * @param CustomerAddressModel $customerAddressModel the model to validate
     * @return InputException
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function _validate(CustomerAddressModel $customerAddressModel)
    {
        $exception = new InputException();
        if ($customerAddressModel->getShouldIgnoreValidation()) {
            return $exception;
        }

        if (!\Zend_Validate::is($customerAddressModel->getFirstname(), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'firstname']);
        }

        if (!\Zend_Validate::is($customerAddressModel->getLastname(), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'lastname']);
        }

        if (!\Zend_Validate::is($customerAddressModel->getStreet(1), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'street']);
        }

        if (!\Zend_Validate::is($customerAddressModel->getCity(), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'city']);
        }

        if (!\Zend_Validate::is($customerAddressModel->getTelephone(), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'telephone']);
        }

        $havingOptionalZip = $this->directoryData->getCountriesWithOptionalZip();
        if (!in_array($customerAddressModel->getCountryId(), $havingOptionalZip)
            && !\Zend_Validate::is($customerAddressModel->getPostcode(), 'NotEmpty')
        ) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'postcode']);
        }

        if (!\Zend_Validate::is($customerAddressModel->getCountryId(), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'countryId']);
        }

        if ($customerAddressModel->getCountryModel()->getRegionCollection()->getSize()
            && !\Zend_Validate::is($customerAddressModel->getRegionId(), 'NotEmpty')
            && $this->directoryData->isRegionRequired($customerAddressModel->getCountryId())
        ) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'regionId']);
        }

        return $exception;
    }
}
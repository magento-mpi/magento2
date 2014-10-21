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
     * @var \Magento\Webapi\Model\DataObjectProcessor
     */
    protected $dataProcessor;

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
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Webapi\Model\DataObjectProcessor $dataProcessor
     * @param \Magento\Customer\Model\AddressRegistry $addressRegistry
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     */
    public function __construct(
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Webapi\Model\DataObjectProcessor $dataProcessor,
        \Magento\Customer\Model\AddressRegistry $addressRegistry,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry
    ) {
        $this->dataProcessor = $dataProcessor;
        $this->addressFactory = $addressFactory;
        $this->addressRegistry = $addressRegistry;
        $this->customerRegistry = $customerRegistry;
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
        }
        if (is_null($addressModel)) {
            $addressData = $this->dataProcessor
                ->buildOutputDataArray($address, '\Magento\Customer\Api\Data\AddressInterface');
            $address = $this->addressFactory->create($addressData);
            $addressModel->setCustomer($customerModel);
        } else {
            $addressModel->updateFromDataModel($address);
        }

        $inputException = $this->_validate($addressModel);
        if ($inputException->wasErrorAdded()) {
            throw $inputException;
        }

        $addressModel->save();
        $this->addressRegistry->push($addressModel);
        return $address->getDataModel(true, true);
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
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Customer\Api\Data\AddressSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
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
     * @param int $addressId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete($addressId)
    {
        $address = $this->addressRegistry->retrieve($addressId);
        $address->delete();
        $address = $this->addressRegistry->remove($addressId);
        return true;
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
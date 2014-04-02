<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

use Magento\Core\Model\StoreManagerInterface;
use Magento\Customer\Model\Converter;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Metadata\Validator;
use Magento\Customer\Model\Resource\Customer\Collection;
use Magento\Event\ManagerInterface;
use Magento\Exception\InputException;
use Magento\Exception\AuthenticationException;
use Magento\Exception\NoSuchEntityException;
use Magento\Exception\StateException;
use Magento\Math\Random;
use Magento\UrlInterface;
use Magento\Service\Data\Filter;
use Magento\Customer\Model\AddressRegistry;

/**
 * Handle various customer account actions
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomerAccountService implements CustomerAccountServiceInterface
{
    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var Data\CustomerBuilder
     */
    private $customerBuilder;

    /**
     * @var Data\CustomerDetailsBuilder
     */
    private $customerDetailsBuilder;

    /**
     * @var Data\SearchResultsBuilder
     */
    private $searchResultsBuilder;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Random
     */
    private $mathRandom;

    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var CustomerAddressServiceInterface
     */
    private $customerAddressService;

    /**
     * @var CustomerMetadataServiceInterface
     */
    private $customerMetadataService;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var AddressRegistry
     */
    private $addressRegistry;

    /**
     * @param CustomerFactory $customerFactory
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param Random $mathRandom
     * @param Converter $converter
     * @param Validator $validator
     * @param Data\CustomerBuilder $customerBuilder
     * @param Data\CustomerDetailsBuilder $customerDetailsBuilder
     * @param Data\SearchResultsBuilder $searchResultsBuilder
     * @param CustomerAddressServiceInterface $customerAddressService
     * @param CustomerMetadataServiceInterface $customerMetadataService
     * @param CustomerRegistry $customerRegistry
     * @param AddressRegistry $addressRegistry
     * @param UrlInterface $url
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        CustomerFactory $customerFactory,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        Random $mathRandom,
        Converter $converter,
        Validator $validator,
        Data\CustomerBuilder $customerBuilder,
        Data\CustomerDetailsBuilder $customerDetailsBuilder,
        Data\SearchResultsBuilder $searchResultsBuilder,
        CustomerAddressServiceInterface $customerAddressService,
        CustomerMetadataServiceInterface $customerMetadataService,
        AddressRegistry $addressRegistry,
        CustomerRegistry $customerRegistry,
        UrlInterface $url
    ) {
        $this->customerFactory = $customerFactory;
        $this->eventManager = $eventManager;
        $this->storeManager = $storeManager;
        $this->mathRandom = $mathRandom;
        $this->converter = $converter;
        $this->validator = $validator;
        $this->customerBuilder = $customerBuilder;
        $this->customerDetailsBuilder = $customerDetailsBuilder;
        $this->searchResultsBuilder = $searchResultsBuilder;
        $this->customerAddressService = $customerAddressService;
        $this->customerMetadataService = $customerMetadataService;
        $this->addressRegistry = $addressRegistry;
        $this->url = $url;
        $this->customerRegistry = $customerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function resendConfirmation($email, $websiteId, $redirectUrl = '')
    {
        $customer = $this->customerRegistry->retrieveByEmail($email, $websiteId);
        if (!$customer->getConfirmation()) {
            throw new StateException('No confirmation needed.', StateException::INVALID_STATE);
        }
        $customer->sendNewAccountEmail(
            self::NEW_ACCOUNT_EMAIL_CONFIRMATION,
            $redirectUrl,
            $this->storeManager->getStore()->getId()
        );
    }
    /**
     * {@inheritdoc}
     */
    public function activateCustomer($customerId, $confirmationKey)
    {
        // load customer by id
        $customer = $this->customerRegistry->retrieve($customerId);

        // check if customer is inactive
        if (!$customer->getConfirmation()) {
            throw new StateException('Account already active', StateException::INVALID_STATE);
        }

        if ($customer->getConfirmation() !== $confirmationKey) {
            throw new StateException('Invalid confirmation token', StateException::INPUT_MISMATCH);
        }
        // activate customer
        $customer->setConfirmation(null);
        $customer->save();
        $customer->sendNewAccountEmail('confirmed', '', $this->storeManager->getStore()->getId());
        return $this->converter->createCustomerFromModel($customer);
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate($username, $password)
    {
        $customerModel = $this->customerFactory->create();
        $customerModel->setWebsiteId($this->storeManager->getStore()->getWebsiteId());
        try {
            $customerModel->authenticate($username, $password);
        } catch (\Magento\Core\Exception $e) {
            switch ($e->getCode()) {
                case CustomerModel::EXCEPTION_EMAIL_NOT_CONFIRMED:
                    $code = AuthenticationException::EMAIL_NOT_CONFIRMED;
                    break;
                case CustomerModel::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                    $code = AuthenticationException::INVALID_EMAIL_OR_PASSWORD;
                    break;
                default:
                    $code = AuthenticationException::UNKNOWN;
            }
            throw new AuthenticationException($e->getMessage(), $code, $e);
        }

        $this->eventManager->dispatch('customer_login', array('customer' => $customerModel));

        return $this->converter->createCustomerFromModel($customerModel);
    }

    /**
     * {@inheritdoc}
     */
    public function validateResetPasswordLinkToken($customerId, $resetPasswordLinkToken)
    {
        $this->validateResetPasswordToken($customerId, $resetPasswordLinkToken);
    }

    /**
     * {@inheritdoc}
     */
    public function initiatePasswordReset($email, $websiteId, $template)
    {
        // load customer by email
        $customer = $this->customerRegistry->retrieveByEmail($email, $websiteId);

        $newPasswordToken = $this->mathRandom->getUniqueHash();
        $customer->changeResetPasswordLinkToken($newPasswordToken);
        $resetUrl = $this->url
            ->getUrl(
                'customer/account/createPassword',
                [
                    '_query' => array('id' => $customer->getId(), 'token' => $newPasswordToken),
                    '_store' => $customer->getStoreId()
                ]
            );

        $customer->setResetPasswordUrl($resetUrl);
        switch ($template) {
            case CustomerAccountServiceInterface::EMAIL_REMINDER:
                $customer->sendPasswordReminderEmail();
                break;
            case CustomerAccountServiceInterface::EMAIL_RESET:
                $customer->sendPasswordResetConfirmationEmail();
                break;
            default:
                throw new InputException(__('Invalid email type.'), InputException::INVALID_FIELD_VALUE);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function resetPassword($customerId, $resetToken, $newPassword)
    {
        $customerModel = $this->validateResetPasswordToken($customerId, $resetToken);
        $customerModel->setRpToken(null);
        $customerModel->setRpTokenCreatedAt(null);
        $customerModel->setPassword($newPassword);
        $customerModel->save();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmationStatus($customerId)
    {
        // load customer by id
        $customer = $this->customerRegistry->retrieve($customerId);
        if (!$customer->getConfirmation()) {
            return CustomerAccountServiceInterface::ACCOUNT_CONFIRMED;
        }
        if ($customer->isConfirmationRequired()) {
            return CustomerAccountServiceInterface::ACCOUNT_CONFIRMATION_REQUIRED;
        }
        return CustomerAccountServiceInterface::ACCOUNT_CONFIRMATION_NOT_REQUIRED;
    }

    /**
     * {@inheritdoc}
     */
    public function createAccount(Data\CustomerDetails $customerDetails, $password = null, $redirectUrl = '')
    {
        $customer = $customerDetails->getCustomer();

        // This logic allows an existing customer to be added to a different store.  No new account is created.
        // The plan is to move this logic into a new method called something like 'registerAccountWithStore'
        if ($customer->getId()) {
            $customerModel = $this->customerRegistry->retrieve($customer->getId());
            if ($customerModel->isInStore($customer->getStoreId())) {
                throw new InputException(__('Customer already exists in this store.'));
            }
        }
        // Make sure we have a storeId to associate this customer with.
        if (!$customer->getStoreId()) {
            if ($customer->getWebsiteId()) {
                $storeId = $this->storeManager->getWebsite($customer->getWebsiteId())->getDefaultStore()->getId();
            } else {
                $storeId = $this->storeManager->getStore()->getId();
            }
            $customer = $this->customerBuilder->populate($customer)
                ->setStoreId($storeId)
                ->create();
        }

        try {
            $customerId = $this->saveCustomer($customer, $password);
        } catch (\Magento\Customer\Exception $e) {
            if ($e->getCode() === CustomerModel::EXCEPTION_EMAIL_EXISTS) {
                throw new StateException(
                    __('Customer with the same email already exists in associated website.'),
                    StateException::INPUT_MISMATCH
                );
            }
            throw $e;
        }

        $this->customerAddressService->saveAddresses($customerId, $customerDetails->getAddresses());

        $customerModel = $this->customerRegistry->retrieve($customerId);

        $newLinkToken = $this->mathRandom->getUniqueHash();
        $customerModel->changeResetPasswordLinkToken($newLinkToken);

        if ($customerModel->isConfirmationRequired()) {
            $customerModel->sendNewAccountEmail(
                self::NEW_ACCOUNT_EMAIL_CONFIRMATION,
                $redirectUrl,
                $customer->getStoreId()
            );
        } else {
            $customerModel->sendNewAccountEmail(
                self::NEW_ACCOUNT_EMAIL_REGISTERED,
                $redirectUrl,
                $customer->getStoreId()
            );
        }
        return $this->converter->createCustomerFromModel($customerModel);
    }

    /**
     * {@inheritdoc}
     */
    public function updateCustomer(Data\CustomerDetails $customerDetails)
    {
        $customer = $customerDetails->getCustomer();
        // Making this call first will ensure the customer already exists.
        $this->getCustomer($customer->getId());
        $this->saveCustomer($customer);

        $addresses = $customerDetails->getAddresses();
        // If $address is null, no changes must made to the list of addresses
        // be careful $addresses != null would be true of $addresses is an empty array
        if ($addresses !== null) {
            $existingAddresses = $this->customerAddressService->getAddresses($customer->getId());
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
    }

    /**
     * (@inheritdoc)
     */
    public function searchCustomers(Data\SearchCriteria $searchCriteria)
    {
        $this->searchResultsBuilder->setSearchCriteria($searchCriteria);

        /** @var Collection $collection */
        $collection = $this->customerFactory->create()->getCollection();
        // This is needed to make sure all the attributes are properly loaded
        foreach ($this->customerMetadataService->getAllCustomerAttributeMetadata() as $metadata) {
            $collection->addAttributeToSelect($metadata->getAttributeCode());
        }
        // Needed to enable filtering on name as a whole
        $collection->addNameToSelect();
        // Needed to enable filtering based on billing address attributes
        $collection->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');
        $this->addFiltersToCollection($searchCriteria->getFilters(), $collection);
        $this->searchResultsBuilder->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $field => $direction) {
                $collection->addOrder($field, $direction == Data\SearchCriteria::SORT_ASC ? 'ASC' : 'DESC');
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        $customersDetails = [];

        /** @var CustomerModel $customerModel */
        foreach ($collection as $customerModel) {
            $customer = $this->converter->createCustomerFromModel($customerModel);
            $addresses = $this->customerAddressService->getAddresses($customer->getId());
            $customerDetails = $this->customerDetailsBuilder
                ->setCustomer($customer)->setAddresses($addresses)->create();
            $customersDetails[] = $customerDetails;
        }
        $this->searchResultsBuilder->setItems($customersDetails);
        return $this->searchResultsBuilder->create();
    }

    /**
     * {@inheritdoc}
     */
    public function saveCustomer(Data\Customer $customer, $password = null)
    {
        $customerModel = $this->converter->createCustomerModel($customer);

        if ($password) {
            $customerModel->setPassword($password);
        } elseif (!$customerModel->getId()) {
            $customerModel->setPassword($customerModel->generatePassword());
        }

        // Shouldn't we be calling validateCustomerData/Details here?
        $this->validate($customerModel);

        $customerModel->save();
        // Clear the customer from registry so that the updated one can be retrieved next time
        $this->customerRegistry->remove($customerModel->getId());

        return $customerModel->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer($customerId)
    {
        $customerModel = $this->customerRegistry->retrieve($customerId);
        return $this->converter->createCustomerFromModel($customerModel);
    }

    /**
     * {@inheritdoc}
     */
    public function changePassword($customerId, $currentPassword, $newPassword)
    {
        $customerModel = $this->customerRegistry->retrieve($customerId);
        if (!$customerModel->validatePassword($currentPassword)) {
            throw new AuthenticationException(
                __("Password doesn't match for this account."),
                AuthenticationException::INVALID_EMAIL_OR_PASSWORD
            );
        }
        $customerModel->setRpToken(null);
        $customerModel->setRpTokenCreatedAt(null);
        $customerModel->setPassword($newPassword);
        $customerModel->save();
        // FIXME: Are we using the proper template here?
        $customerModel->sendPasswordResetNotificationEmail();
    }

    /**
     * {@inheritdoc}
     */
    public function validateCustomerData(Data\Customer $customer, array $attributes = [])
    {
        $customerErrors = $this->validator->validateData(
            \Magento\Service\DataObjectConverter::toFlatArray($customer),
            $attributes,
            'customer'
        );

        if ($customerErrors !== true) {
            return array(
                'error'     => -1,
                'message'   => implode(', ', $this->validator->getMessages())
            );
        }

        $customerModel = $this->converter->createCustomerModel($customer);

        $result = $customerModel->validate();
        if (true !== $result && is_array($result)) {
            return array(
                'error'   => -1,
                'message' => implode(', ', $result)
            );
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function canModify($customerId)
    {
        $customerModel = $this->customerRegistry->retrieve($customerId);
        return !$customerModel->isReadonly();
    }

    /**
     * {@inheritdoc}
     */
    public function canDelete($customerId)
    {
        $customerModel = $this->customerRegistry->retrieve($customerId);
        return $customerModel->isDeleteable();
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerDetails($customerId)
    {
        return $this->customerDetailsBuilder
            ->setCustomer($this->getCustomer($customerId))
            ->setAddresses($this->customerAddressService->getAddresses($customerId))
            ->create();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCustomer($customerId)
    {
        $customerModel = $this->customerRegistry->retrieve($customerId);
        foreach ($customerModel->getAddresses() as $addressModel) {
            $this->addressRegistry->remove($addressModel->getId());
        }
        $customerModel->delete();
        $this->customerRegistry->remove($customerId);
    }

    /**
     * {@inheritdoc}
     */
    public function isEmailAvailable($customerEmail, $websiteId)
    {
        try {
            $this->customerRegistry->retrieveByEmail($customerEmail, $websiteId);
            return false;
        } catch (NoSuchEntityException $e) {
            return true;
        }
    }

    /**
     * Adds some filters from a filter group to a collection.
     *
     * @param Data\Search\FilterGroupInterface $filterGroup
     * @param Collection $collection
     * @return void
     * @throws \Magento\Exception\InputException
     */
    protected function addFiltersToCollection(Data\Search\FilterGroupInterface $filterGroup, Collection $collection)
    {
        if (strcasecmp($filterGroup->getGroupType(), 'AND')) {
            throw new InputException('Only AND grouping is currently supported for filters.');
        }

        foreach ($filterGroup->getFilters() as $filter) {
            $this->addFilterToCollection($collection, $filter);
        }

        foreach ($filterGroup->getGroups() as $group) {
            $this->addFilterGroupToCollection($collection, $group);
        }
    }

    /**
     * Helper function that adds a filter to the collection
     *
     * @param Collection $collection
     * @param Filter $filter
     * @return void
     */
    protected function addFilterToCollection(Collection $collection, Filter $filter)
    {
        $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
        $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param Collection $collection
     * @param Data\Search\FilterGroupInterface $group
     * @return void
     * @throws \Magento\Exception\InputException
     */
    protected function addFilterGroupToCollection(Collection $collection, Data\Search\FilterGroupInterface $group)
    {
        if (strcasecmp($group->getGroupType(), 'OR')) {
            throw new InputException('The only nested groups currently supported for filters are of type OR.');
        }
        $fields = [];
        $conditions = [];
        foreach ($group->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = ['attribute' => $filter->getField(), $condition => $filter->getValue()];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * Validate customer attribute values.
     *
     * @param CustomerModel $customerModel
     * @throws InputException
     * @return void
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function validate(CustomerModel $customerModel)
    {
        $exception = new InputException();
        if (!\Zend_Validate::is(trim($customerModel->getFirstname()), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, 'firstname', '');
        }

        if (!\Zend_Validate::is(trim($customerModel->getLastname()), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, 'lastname', '');
        }

        if (!\Zend_Validate::is($customerModel->getEmail(), 'EmailAddress')) {
            $exception->addError(InputException::INVALID_FIELD_VALUE, 'email', $customerModel->getEmail());
        }

        $dob = $this->getAttributeMetadata('dob');
        if (!is_null($dob) && $dob->isRequired() && '' == trim($customerModel->getDob())) {
            $exception->addError(InputException::REQUIRED_FIELD, 'dob', '');
        }

        $taxvat = $this->getAttributeMetadata('taxvat');
        if (!is_null($taxvat) && $taxvat->isRequired() && '' == trim($customerModel->getTaxvat())) {
            $exception->addError(InputException::REQUIRED_FIELD, 'taxvat', '');
        }

        $gender = $this->getAttributeMetadata('gender');
        if (!is_null($gender) && $gender->isRequired() && '' == trim($customerModel->getGender())) {
            $exception->addError(InputException::REQUIRED_FIELD, 'gender', '');
        }

        if ($exception->getErrors()) {
            throw $exception;
        }
    }

    /**
     * Validate the Reset Password Token for a customer.
     *
     * @param int $customerId
     * @param string $resetPasswordLinkToken
     * @return CustomerModel
     * @throws \Magento\Exception\StateException If token is expired or mismatched
     * @throws \Magento\Exception\InputException If token or customer id is invalid
     * @throws \Magento\Exception\NoSuchEntityException If customer doesn't exist
     */
    private function validateResetPasswordToken($customerId, $resetPasswordLinkToken)
    {
        if (!is_int($customerId) || empty($customerId) || $customerId < 0) {
            throw InputException::create(
                InputException::INVALID_FIELD_VALUE,
                'customerId',
                $customerId
            );
        }
        if (!is_string($resetPasswordLinkToken) || empty($resetPasswordLinkToken)) {
            throw InputException::create(
                InputException::INVALID_FIELD_VALUE,
                'resetPasswordLinkToken',
                $resetPasswordLinkToken
            );
        }

        $customerModel = $this->customerRegistry->retrieve($customerId);
        $customerToken = $customerModel->getRpToken();

        if (strcmp($customerToken, $resetPasswordLinkToken) !== 0) {
            throw new StateException('Reset password token mismatch.', StateException::INPUT_MISMATCH);
        } else if ($customerModel->isResetPasswordLinkTokenExpired($customerId)) {
            throw new StateException('Reset password token expired.', StateException::EXPIRED);
        }

        return $customerModel;
    }

    /**
     * @param string $attributeCode
     * @return Data\Eav\AttributeMetadata|null
     */
    private function getAttributeMetadata($attributeCode)
    {
        try {
            return $this->customerMetadataService->getCustomerAttributeMetadata($attributeCode);
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}

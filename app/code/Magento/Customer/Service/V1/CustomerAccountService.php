<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

use Magento\Customer\Service\V1\Data\CustomerDetails;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Converter;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Metadata\Validator;
use Magento\Customer\Model\Resource\Customer\Collection;
use Magento\Service\V1\Data\Search\FilterGroup;
use Magento\Event\ManagerInterface;
use Magento\Exception\InputException;
use Magento\Exception\AuthenticationException;
use Magento\Exception\NoSuchEntityException;
use Magento\Exception\StateException;
use Magento\Mail\Exception as MailException;
use Magento\Math\Random;
use Magento\Service\V1\Data\SearchCriteria;
use Magento\UrlInterface;
use Magento\Logger;
use Magento\Encryption\EncryptorInterface as Encryptor;
use Magento\Customer\Model\Config\Share as ConfigShare;
use Magento\Customer\Model\AddressRegistry;
use Magento\Service\V1\Data\Filter;

/**
 * Handle various customer account actions
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
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
     * @var Logger
     */
    protected $logger;

    /**
     * @var Encryptor
     */
    private $encryptor;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var AddressRegistry
     */
    private $addressRegistry;

    /**
     * @var ConfigShare
     */
    private $configShare;

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
     * @param Data\CustomerValidationResultsBuilder $customerValidationResultsBuilder
     * @param CustomerAddressServiceInterface $customerAddressService
     * @param CustomerMetadataServiceInterface $customerMetadataService
     * @param CustomerRegistry $customerRegistry
     * @param AddressRegistry $addressRegistry
     * @param UrlInterface $url
     * @param Logger $logger
     * @param Encryptor $encryptor
     * @param ConfigShare $configShare
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
        Data\CustomerValidationResultsBuilder $customerValidationResultsBuilder,
        CustomerAddressServiceInterface $customerAddressService,
        CustomerMetadataServiceInterface $customerMetadataService,
        CustomerRegistry $customerRegistry,
        AddressRegistry $addressRegistry,
        UrlInterface $url,
        Logger $logger,
        Encryptor $encryptor,
        ConfigShare $configShare
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
        $this->customerValidationResultsBuilder = $customerValidationResultsBuilder;
        $this->customerAddressService = $customerAddressService;
        $this->customerMetadataService = $customerMetadataService;
        $this->customerRegistry = $customerRegistry;
        $this->addressRegistry = $addressRegistry;
        $this->url = $url;
        $this->logger = $logger;
        $this->encryptor = $encryptor;
        $this->configShare = $configShare;
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

        try {
            $customer->sendNewAccountEmail(
                self::NEW_ACCOUNT_EMAIL_CONFIRMATION,
                $redirectUrl,
                $this->storeManager->getStore()->getId()
            );
        } catch (MailException $e) {
            // If we are not able to send a new account email, this should be ignored
            $this->logger->logException($e);
        }
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
        } catch (\Magento\Framework\Model\Exception $e) {
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

        $customerDto = $this->converter->createCustomerFromModel($customerModel);
        $this->eventManager->dispatch('customer_data_object_login', array('customer' => $customerDto));

        return $customerDto;
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
    public function initiatePasswordReset($email, $template, $websiteId = null)
    {
        if (is_null($websiteId)) {
            $websiteId = $this->storeManager->getStore()->getWebsiteId();
        }
        // load customer by email
        $customer = $this->customerRegistry->retrieveByEmail($email, $websiteId);

        $newPasswordToken = $this->mathRandom->getUniqueHash();
        $customer->changeResetPasswordLinkToken($newPasswordToken);
        $resetUrl = $this->url->getUrl(
            'customer/account/createPassword',
            [
                '_query' => array('id' => $customer->getId(), 'token' => $newPasswordToken),
                '_store' => $customer->getStoreId()
            ]
        );

        $customer->setResetPasswordUrl($resetUrl);
        try {
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
        } catch (MailException $e) {
            // If we are not able to send a reset password email, this should be ignored
            $this->logger->logException($e);
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
        $customerModel->setPasswordHash($this->getPasswordHash($newPassword));
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
    public function createCustomer(
        Data\CustomerDetails $customerDetails,
        $password = null,
        $hash = null,
        $redirectUrl = ''
    ) {
        $customer = $customerDetails->getCustomer();

        // This logic allows an existing customer to be added to a different store.  No new account is created.
        // The plan is to move this logic into a new method called something like 'registerAccountWithStore'
        if ($customer->getId()) {
            $customerModel = $this->customerRegistry->retrieve($customer->getId());
            $websiteId = $customerModel->getWebsiteId();

            if ($this->isCustomerInStore($websiteId, $customer->getStoreId())) {
                throw new InputException(__('Customer already exists in this store.'));
            }

            if (empty($password) && empty($hash)) {
                // Reuse existing password
                $hash = $this->converter->getCustomerModel($customer->getId())->getPasswordHash();
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
            $customerId = $this->saveCustomer($customer, $password, $hash);
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
        $this->_sendEmailConfirmation($customerModel, $customer, $redirectUrl);

        return $this->converter->createCustomerFromModel($customerModel);
    }

    /**
     * Send either confirmation or welcome email after an account creation
     *
     * @param CustomerModel $customerModel
     * @param Data\Customer $customer
     * @param string        $redirectUrl
     * @return void
     */
    protected function _sendEmailConfirmation(CustomerModel $customerModel, Data\Customer $customer, $redirectUrl)
    {
        try {
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
        } catch (MailException $e) {
            // If we are not able to send a new account email, this should be ignored
            $this->logger->logException($e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateCustomer(Data\CustomerDetails $customerDetails)
    {
        $customer = $customerDetails->getCustomer();
        // Making this call first will ensure the customer already exists.
        $this->customerRegistry->retrieve($customer->getId());

        $this->saveCustomer(
            $customer,
            null,
            $this->converter->getCustomerModel($customer->getId())->getPasswordHash()
        );

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

        return true;
    }

    /**
     * (@inheritdoc)
     */
    public function searchCustomers(SearchCriteria $searchCriteria)
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
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left')
            ->joinAttribute('company', 'customer_address/company', 'default_billing', null, 'left');
        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $this->searchResultsBuilder->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $field => $direction) {
                $collection->addOrder($field, $direction == SearchCriteria::SORT_ASC ? 'ASC' : 'DESC');
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
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return void
     * @throws \Magento\Exception\InputException
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
     * Create or update customer information
     *
     * @param \Magento\Customer\Service\V1\Data\Customer $customer
     * @param string $password Plain text password
     * @param string $hash Hashed password ready to be saved
     * @throws \Magento\Customer\Exception If something goes wrong during save
     * @throws \Magento\Exception\InputException If bad input is provided
     * @return int customer ID
     */
    protected function saveCustomer(
        \Magento\Customer\Service\V1\Data\Customer $customer,
        $password = null,
        $hash = null
    ) {
        $customerModel = $this->converter->createCustomerModel($customer);

        // Priority: hash, password, auto generated password
        if ($hash) {
            $customerModel->setPasswordHash($hash);
        } elseif ($password) {
            $passwordHash = $this->getPasswordHash($password);
            $customerModel->setPasswordHash($passwordHash);
        } elseif (!$customerModel->getId()) {
            $passwordHash = $this->getPasswordHash($customerModel->generatePassword());
            $customerModel->setPasswordHash($passwordHash);
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
        $customerModel->setPasswordHash($this->getPasswordHash($newPassword));
        $customerModel->save();
        // FIXME: Are we using the proper template here?
        $customerModel->sendPasswordResetNotificationEmail();
    }

    /**
     * {@inheritDoc}
     */
    public function getPasswordHash($password)
    {
        return $this->encryptor->getHash($password, true);
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
            return $this->customerValidationResultsBuilder
                ->setIsValid(false)
                ->setMessages($this->validator->getMessages())
                ->create();
        }

        $customerModel = $this->converter->createCustomerModel($customer);

        $result = $customerModel->validate();
        if (true !== $result && is_array($result)) {
            return $this->customerValidationResultsBuilder
                ->setIsValid(false)
                ->setMessages($result)
                ->create();
        }
        return $this->customerValidationResultsBuilder
            ->setIsValid(true)
            ->setMessages([])
            ->create();
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

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmailAvailable($customerEmail, $websiteId = null)
    {
        try {
            if (is_null($websiteId)) {
                $websiteId = $this->storeManager->getStore()->getWebsiteId();
            }
            $this->customerRegistry->retrieveByEmail($customerEmail, $websiteId);
            return false;
        } catch (NoSuchEntityException $e) {
            return true;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isCustomerInStore($customerWebsiteId, $storeId)
    {
        $ids = [];
        if ((bool)$this->configShare->isWebsiteScope()) {
            $ids = $this->storeManager->getWebsite($customerWebsiteId)->getStoreIds();
        } else {
            foreach ($this->storeManager->getStores() as $store) {
                $ids[] = $store->getId();
            }
        }

        return in_array($storeId, $ids);
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
            throw InputException::create(InputException::INVALID_FIELD_VALUE, 'customerId', $customerId);
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

    /**
     * {@inheritDoc}
     */
    public function getCustomerByEmail($customerEmail, $websiteId = null)
    {
        $customerModel = $this->customerRegistry->retrieveByEmail($customerEmail, $websiteId);
        return $this->converter->createCustomerFromModel($customerModel);
    }

    /**
     * {inheritDoc}
     */
    public function getCustomerDetailsByEmail($customerEmail, $websiteId = null)
    {
        $customerData = $this->getCustomerByEmail($customerEmail, $websiteId);
        return $this->customerDetailsBuilder
            ->setCustomer($customerData)
            ->setAddresses($this->customerAddressService->getAddresses($customerData->getId()))
            ->create();
    }

    /**
     * {@inheritdoc}
     */
    public function updateCustomerDetailsByEmail(
        $customerEmail,
        CustomerDetails $customerDetails,
        $websiteId = null
    ) {
        $customerData = $customerDetails->getCustomer();
        $customer = $this->getCustomerByEmail($customerEmail, $websiteId);
        if ($customerData->getId() && $customerData->getId() !== $customer->getId()) {
            throw new StateException('Altering the customer ID is not permitted');
        }

        $customerData = $this->customerBuilder->populate($customerData)
            ->setId($customer->getId())
            ->create();
        $customerDetails = $this->customerDetailsBuilder->populate($customerDetails)
            ->setCustomer($customerData)
            ->create();

        return $this->updateCustomer($customerDetails);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCustomerByEmail($customerEmail, $websiteId = null)
    {
        $customerModel = $this->customerRegistry->retrieveByEmail($customerEmail, $websiteId);
        $customerId = $customerModel->getId();
        $customerModel->delete();
        $this->customerRegistry->remove($customerId);

        return true;
    }
}

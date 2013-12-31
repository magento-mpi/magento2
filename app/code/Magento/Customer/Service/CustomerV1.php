<?php
namespace Magento\Customer\Service;

use \Magento\Core\Exception as ExceptionCore;
use \Magento\Customer\Model\Customer as CustomerModel;

/**
 * Customer Service Implementation
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class CustomerV1 implements CustomerV1Interface
{
    const CUSTOMER_ATTRIBUTE_SET_ID = 1;
    const ADDRESS_ATTRIBUTE_SET_ID = 2;

    /** @var \Magento\Customer\Model\CustomerFactory */
    protected $_customerFactory;

    /** @var \Magento\Customer\Model\AddressFactory */
    protected $_addressFactory;

    /** @var Eav\AttributeMetadataServiceV1Interface */
    protected $_eavMetadataService;

    /** @var array Cache of DTOs */
    protected $_cache = [];

    /**
     * Core event manager proxy
     *
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /** @var \Magento\Core\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * @var \Magento\Math\Random
     */
    protected $_mathRandom;

    /**
     * @var \Magento\Customer\Model\Converter
     */
    protected $_converter;

    /**
     * @var \Magento\Customer\Model\Metadata\Validator
     */
    protected $_validator;

    /**
     * Constructor
     *
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param Eav\AttributeMetadataServiceV1Interface $eavMetadataService
     * @param \Magento\Core\Model\Email\Template\MailerFactory $mailerFactory
     * @param \Magento\Core\Model\Email\InfoFactory $emailInfoFactory
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Math\Random $mathRandom
     * @param \Magento\Customer\Model\Converter $converter
     * @param \Magento\Customer\Model\Metadata\Validator $validator
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        Eav\AttributeMetadataServiceV1Interface $eavMetadataService,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Math\Random $mathRandom,
        \Magento\Customer\Model\Converter $converter,
        \Magento\Customer\Model\Metadata\Validator $validator
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_addressFactory = $addressFactory;
        $this->_eavMetadataService = $eavMetadataService;
        $this->_eventManager = $eventManager;
        $this->_storeManager = $storeManager;
        $this->_mathRandom = $mathRandom;
        $this->_converter = $converter;
        $this->_validator = $validator;
    }

    /**
     * @inheritdoc
     */
    public function getAddresses($customerId)
    {
        //TODO: use cache MAGETWO-16862
        $customer = $this->_getCustomerModel($customerId);
        $addresses = $customer->getAddresses();
        $defaultBillingId = $customer->getDefaultBilling();
        $defaultShippingId = $customer->getDefaultShipping();

        $result = array();
        /** @var $address \Magento\Customer\Model\Address */
        foreach ($addresses as $address) {
            $result[] = $this->_createAddress(
                $address,
                $defaultBillingId,
                $defaultShippingId
            );
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultBillingAddress($customerId)
    {
        //TODO: use cache MAGETWO-16862
        $customer = $this->_getCustomerModel($customerId);
        $address = $customer->getDefaultBillingAddress();
        if ($address === false) {
            return null;
        }
        return $this->_createAddress(
            $address,
            $customer->getDefaultBilling(),
            $customer->getDefaultShipping()
        );
    }

    /**
     * @inheritdoc
     */
    public function getDefaultShippingAddress($customerId)
    {
        //TODO: use cache MAGETWO-16862
        $customer = $this->_getCustomerModel($customerId);
        $address = $customer->getDefaultShippingAddress();
        if ($address === false) {
            return null;
        }
        return $this->_createAddress($address,
            $customer->getDefaultBilling(),
            $customer->getDefaultShipping()
        );
    }

    /**
     * @inheritdoc
     */
    public function getAddressById($customerId, $addressId)
    {
        //TODO: use cache MAGETWO-16862
        $customer = $this->_getCustomerModel($customerId);
        $address = $customer->getAddressById($addressId);
        if (!$address->getId()) {
            throw new Entity\V1\Exception(
                'Address id ' . $addressId . ' not found',
                CustomerV1Interface::CODE_ADDRESS_NOT_FOUND
            );
        }
        return $this->_createAddress(
            $address,
            $customer->getDefaultBilling(),
            $customer->getDefaultShipping()
        );
    }

    /**
     * Create address based on model
     *
     * @param \Magento\Customer\Model\Address $addressModel
     * @param int $defaultBillingId
     * @param int $defaultShippingId
     * @return Entity\V1\Address
     */
    protected function _createAddress(\Magento\Customer\Model\Address $addressModel,
        $defaultBillingId, $defaultShippingId
    ) {
        $address = new Entity\V1\Address();
        $addressId = $addressModel->getId();
        $address->setId($addressId)
            ->setStreet($addressModel->getStreet())
            ->setDefaultBilling($addressId === $defaultBillingId)
            ->setDefaultShipping($addressId === $defaultShippingId)
            ->setCustomerId($addressModel->getCustomerId())
            ->setRegion(
                new Entity\V1\Region(
                    $addressModel->getRegionCode(),
                    $addressModel->getRegion(),
                    $addressModel->getRegionId()
                )
            );
        $validAttributes = array_merge(
            $addressModel->getDefaultAttributeCodes(),
            [
                'id', 'region_id', 'region', 'street', 'vat_is_valid',
                'default_billing', 'default_shipping',
                //TODO: create VAT object at MAGETWO-16860
                'vat_request_id', 'vat_request_date', 'vat_request_success'
            ]
        );
        foreach ($addressModel->getAttributes() as $attribute) {
            $code = $attribute->getAttributeCode();
            if (!in_array($code, $validAttributes) && $addressModel->getData($code) !== null) {
                $address->setAttribute($code, $addressModel->getData($code));
            }
        }

        return $address;
    }


    /**
     * @inheritdoc
     */
    public function deleteAddressFromCustomer($customerId, $addressId)
    {
        if (!$addressId) {
            throw new Entity\V1\Exception('Invalid addressId', CustomerV1Interface::CODE_INVALID_ADDRESS_ID);
        }

        $address = $this->_addressFactory->create();
        $address->load($addressId);

        if (!$address->getId()) {
            throw new Entity\V1\Exception(
                'Address id ' . $addressId . ' not found',
                CustomerV1Interface::CODE_ADDRESS_NOT_FOUND
            );
        }

        // Validate address_id <=> customer_id
        if ($address->getCustomerId() != $customerId) {
            throw new Entity\V1\Exception(
                'The address does not belong to this customer',
                CustomerV1Interface::CODE_CUSTOMER_ID_MISMATCH
            );
        }

        $address->delete();
    }

    /**
     * @inheritdoc
     */
    public function getAddressAttributeMetadata($attributeCode)
    {
        return $this->_eavMetadataService->getAttributeMetadata('customer_address', $attributeCode);
    }

    /**
     * @inheritdoc
     */
    public function getAllAddressAttributeMetadata()
    {
        return $this->_eavMetadataService
            ->getAllAttributeSetMetadata('customer_address', self::ADDRESS_ATTRIBUTE_SET_ID);
    }

    /**
     * @inheritdoc
     */
    public function saveAddresses($customerId, array $addresses)
    {
        $customerModel = $this->_getCustomerModel($customerId);
        $addressModels = [];

        $aggregateException = new Entity\V1\AggregateException("All validation exceptions for all addresses.",
            self::CODE_VALIDATION_FAILED);
        foreach ($addresses as $address) {
            $addressModel = null;
            if ($address->getId()) {
                $addressModel = $customerModel->getAddressItemById($address->getId());
            }
            if (is_null($addressModel)) {
                $addressModel = $this->_addressFactory->create();
                $addressModel->setCustomer($customerModel);
            }
            $this->_updateAddressModel($addressModel, $address);

            $validationErrors = $addressModel->validate();
            if ($validationErrors !== true) {
                $aggregateException->pushException(
                    new Entity\V1\Exception(
                        'There were one or more errors validating the address with id ' . $address->getId(),
                        self::CODE_VALIDATION_FAILED,
                        new \Magento\Validator\ValidatorException([$validationErrors])
                    )
                );
                continue;
            }
            $addressModels[] = $addressModel;
        }
        if ($aggregateException->hasExceptions()) {
            throw $aggregateException;
        }
        $addressIds = [];

        foreach ($addressModels as $addressModel) {
            try {
                $addressModel->save();
                $addressIds[] = $addressModel->getId();
            } catch (\Exception $e) {
                switch ($e->getCode()) {
                    case CustomerModel::EXCEPTION_EMAIL_EXISTS:
                        $code = self::CODE_EMAIL_EXISTS;
                        break;
                    default:
                        $code = self::CODE_UNKNOWN;
                }
                throw new Entity\V1\Exception($e->getMessage(), $code, $e);
            }
        }

        return $addressIds;
    }

    /**
     * @inheritdoc
     */
    public function getCustomer($customerId)
    {
        if (!isset($this->_cache[$customerId])) {
            $customerModel = $this->_getCustomerModel($customerId);
            $customerEntity = $this->_converter->createCustomerFromModel($customerModel);
            $customerEntity->lock();
            $this->_cache[$customerId] = $customerEntity;
        }

        return $this->_cache[$customerId];
    }

    /**
     * @inheritdoc
     */
    public function sendConfirmation($email)
    {
        $customer = $this->_customerFactory->create();
        $customer->setWebsiteId($this->_storeManager->getStore()->getWebsiteId())->loadByEmail($email);
        if (!$customer->getId()) {
            throw new Entity\V1\Exception('Wrong email.', CustomerV1Interface::CODE_EMAIL_NOT_FOUND);
        }
        if ($customer->getConfirmation()) {
            $customer->sendNewAccountEmail('confirmation', '', $this->_storeManager->getStore()->getId());
        } else {
            throw new Entity\V1\Exception(
                'This email does not require confirmation.',
                CustomerV1Interface::CODE_CONFIRMATION_NOT_NEEDED
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function saveCustomer(Entity\V1\Customer $customer, $password = null)
    {
        $customerModel = $this->_createCustomerModel($customer);

        if ($password) {
            $customerModel->setPassword($password);
        }

        $validationErrors = $customerModel->validate();
        if ($validationErrors !== true) {
            throw new Entity\V1\Exception(
                'There were one or more errors validating the customer object.',
                self::CODE_VALIDATION_FAILED,
                new \Magento\Validator\ValidatorException([$validationErrors])
            );
        }

        try {
            $customerModel->save();
            unset($this->_cache[$customerModel->getId()]);
        } catch (\Exception $e) {
            switch ($e->getCode()) {
                case CustomerModel::EXCEPTION_EMAIL_EXISTS:
                    $code = self::CODE_EMAIL_EXISTS;
                    break;
                default:
                    $code = self::CODE_UNKNOWN;
            }
            throw new Entity\V1\Exception($e->getMessage(), $code, $e);
        }

        return $customerModel->getId();
    }

    /**
     * @inheritdoc
     */
    public function activateAccount($customerId, $key)
    {
        // load customer by id
        $customer = $this->_getCustomerModel($customerId);

        // check if customer is inactive
        if ($customer->getConfirmation()) {
            if ($customer->getConfirmation() !== $key) {
                throw new ExceptionCore('Wrong confirmation key.');
            }

            // activate customer
            try {
                $customer->setConfirmation(null);
                $customer->save();
            } catch (\Exception $e) {
                throw new ExceptionCore('Failed to confirm customer account.');
            }
            $customer->sendNewAccountEmail('confirmed', '', $this->_storeManager->getStore()->getId());
        } else {
            throw new Entity\V1\Exception(
                'Customer account is already active.',
                CustomerV1Interface::CODE_ACCT_ALREADY_ACTIVE
            );
        }

        return $this->_converter->createCustomerFromModel($customer);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerAttributeMetadata($attributeCode)
    {
        return $this->_eavMetadataService->getAttributeMetadata('customer', $attributeCode);
    }

    /**
     * @inheritdoc
     */
    public function getAllCustomerAttributeMetadata()
    {
        return $this->_eavMetadataService->getAllAttributeSetMetadata('customer', self::CUSTOMER_ATTRIBUTE_SET_ID);
    }

    /**
     * @inheritdoc
     */
    public function authenticate($username, $password)
    {
        $customerModel = $this->_customerFactory->create();
        $customerModel->setWebsiteId($this->_storeManager->getStore()->getWebsiteId());
        try {
            $customerModel->authenticate($username, $password);
        } catch (\Magento\Core\Exception $e) {
            switch ($e->getCode()) {
                case CustomerModel::EXCEPTION_EMAIL_NOT_CONFIRMED:
                    $code = self::CODE_EMAIL_NOT_CONFIRMED;
                    break;
                case CustomerModel::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                    $code = self::CODE_INVALID_EMAIL_OR_PASSWORD;
                    break;
                default:
                    $code = self::CODE_UNKNOWN;
            }
            throw new Entity\V1\Exception($e->getMessage(), $code, $e);
        }

        $this->_eventManager->dispatch('customer_login', array('customer'=>$customerModel));

        return $this->_converter->createCustomerFromModel($customerModel);
    }

    /**
     * @inheritdoc
     */
    public function validateResetPasswordLinkToken($customerId, $resetPasswordLinkToken)
    {
        $this->_validateResetPasswordToken($customerId, $resetPasswordLinkToken);
    }

    /**
     * @inheritdoc
     */
    public function sendPasswordResetLink($email, $websiteId)
    {
        $customer = $this->_customerFactory->create()
            ->setWebsiteId($websiteId)
            ->loadByEmail($email);

        if (!$customer->getId()) {
            throw new Entity\V1\Exception(
                'No customer found for the provided email and website ID.', self::CODE_EMAIL_NOT_FOUND);
        }
        try {
            $newPasswordToken = $this->_mathRandom->getUniqueHash();
            $customer->changeResetPasswordLinkToken($newPasswordToken);
            $customer->sendPasswordResetConfirmationEmail();
        } catch (\Exception $exception) {
            throw new Entity\V1\Exception($exception->getMessage(), self::CODE_UNKNOWN, $exception);
        }
    }

    /**
     * @inheritdoc
     */
    public function resetPassword($customerId, $password, $resetToken)
    {
        $customerModel = $this->_validateResetPasswordToken($customerId, $resetToken);
        $customerModel->setRpToken(null);
        $customerModel->setRpTokenCreatedAt(null);
        $customerModel->setPassword($password);
        $customerModel->save();
    }

    /**
     * @inheritdoc
     */
    public function createAccount(
        Entity\V1\Customer $customer,
        array $addresses,
        $password = null,
        $confirmationBackUrl = '',
        $registeredBackUrl = '',
        $storeId = 0
    ) {
        $customerId = $customer->getCustomerId();
        if ($customerId) {
            $customerModel = $this->_getCustomerModel($customerId);
            if ($customerModel->isInStore($storeId)) {
                return new Entity\V1\Response\CreateCustomerAccountResponse($customerId, '');;
            }
        }
        $customerId = $this->saveCustomer($customer, $password);
        $this->saveAddresses($customerId, $addresses);

        $customerModel = $this->_getCustomerModel($customerId);

        $newLinkToken = $this->_mathRandom->getUniqueHash();
        $customerModel->changeResetPasswordLinkToken($newLinkToken);

        if (!$storeId) {
            $storeId = $this->_storeManager->getStore()->getId();
        }

        if ($customerModel->isConfirmationRequired()) {
            $customerModel->sendNewAccountEmail('confirmation', $confirmationBackUrl, $storeId);
            return new Entity\V1\Response\CreateCustomerAccountResponse($customerId, self::ACCOUNT_CONFIRMATION);
        } else {
            $customerModel->sendNewAccountEmail('registered', $registeredBackUrl, $storeId);
            return new Entity\V1\Response\CreateCustomerAccountResponse($customerId, self::ACCOUNT_REGISTERED);
        }
    }

    /**
     * @inheritdoc
     */
    public function validateCustomerData(Entity\V1\Customer $customer, array $attributes)
    {
        $customerErrors = $this->_validator->validateData(
            $customer->__toArray(),
            $attributes,
            'customer'
        );

        if ($customerErrors !== true) {
            return array(
                'error'     => -1,
                'message'   => implode(', ', $customerErrors)
            );
        }

        $customerModel = $this->_createCustomerModel($customer);

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
     * @param int $customerId
     * @throws Entity\V1\Exception If customerId is not found or other error occurs.
     * @return CustomerModel
     */
    protected function _getCustomerModel($customerId)
    {
        try {
            $customer = $this->_customerFactory->create()->load($customerId);
        } catch (\Exception $e) {
            throw new Entity\V1\Exception($e->getMessage(), $e->getCode(), $e);
        }

        if (!$customer->getId()) {
            // customer does not exist
            throw new Entity\V1\Exception(
                'No customer with customerId ' . $customerId . ' exists.',
                self::CODE_INVALID_CUSTOMER_ID
            );
        } else {
            return $customer;
        }
    }

    /**
     * Updates an Address Model based on information from an Address DTO.
     *
     * @param \Magento\Customer\Model\Address $addressModel
     * @param Entity\V1\Address $address
     * return null
     */
    private function _updateAddressModel(\Magento\Customer\Model\Address $addressModel, Entity\V1\Address $address)
    {
        // Set all attributes
        foreach ($address->getAttributes() as $attributeCode => $attributeData) {
            if ('region' == $attributeCode
                && $address->getRegion() instanceof \Magento\Customer\Service\Entity\V1\Region
            ) {
                $addressModel->setData('region', $address->getRegion()->getRegion());
                $addressModel->setData('region_code', $address->getRegion()->getRegionCode());
                $addressModel->setData('region_id', $address->getRegion()->getRegionId());
            } else {
                $addressModel->setData($attributeCode, $attributeData);
            }
        }
        // Set customer related data
        $isBilling = $address->isDefaultBilling();
        $addressModel->setIsDefaultBilling($isBilling);
        $addressModel->setIsDefaultShipping($address->isDefaultShipping());
        // Need to use attribute set or future updates can cause data loss
        if (!$addressModel->getAttributeSetId()) {
            $addressModel->setAttributeSetId(self::ADDRESS_ATTRIBUTE_SET_ID);
        }
    }

    /**
     * Creates a customer model from a customer entity.
     *
     * @param Entity\V1\Customer $customer
     * @return CustomerModel
     */
    protected function _createCustomerModel(Entity\V1\Customer $customer)
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
            $customerModel->setAttributeSetId(self::CUSTOMER_ATTRIBUTE_SET_ID);
            return $customerModel;
        }

        return $customerModel;
    }

    /**
     * @param $customerId
     * @param $resetPasswordLinkToken
     * @return \Magento\Customer\Model\Customer
     * @throws Entity\V1\Exception
     */
    protected function _validateResetPasswordToken($customerId, $resetPasswordLinkToken)
    {
        if (!is_int($customerId)
            || !is_string($resetPasswordLinkToken)
            || empty($resetPasswordLinkToken)
            || empty($customerId)
            || $customerId < 0
        ) {
            throw new Entity\V1\Exception('Invalid password reset token.', self::CODE_INVALID_RESET_TOKEN);
        }

        $customerModel = $this->_getCustomerModel($customerId);

        $customerToken = $customerModel->getRpToken();
        if (strcmp($customerToken, $resetPasswordLinkToken) !== 0
            || $customerModel->isResetPasswordLinkTokenExpired($customerId)
        ) {
            throw new Entity\V1\Exception('Your password reset link has expired.', self::CODE_RESET_TOKEN_EXPIRED);
        }

        return $customerModel;
    }
}

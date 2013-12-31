<?php

namespace Magento\Customer\Service;

/**
 * Customer Service Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface CustomerV1Interface
{
    /** account response status */
    const ACCOUNT_CONFIRMATION = "confirmation";
    const ACCOUNT_REGISTERED = "registered";

    /** Error codes */
    const CODE_UNKNOWN                              = 0;
    const CODE_ACCT_ALREADY_ACTIVE                  = 1;
    const CODE_INVALID_RESET_TOKEN                  = 2;
    const CODE_RESET_TOKEN_EXPIRED                  = 3;
    const CODE_EMAIL_NOT_FOUND                      = 4;
    const CODE_CONFIRMATION_NOT_NEEDED              = 5;
    const CODE_CUSTOMER_ID_MISMATCH                 = 6;
    const CODE_EMAIL_NOT_CONFIRMED                  = 7;
    const CODE_INVALID_EMAIL_OR_PASSWORD            = 8;
    const CODE_EMAIL_EXISTS                         = 9;
    const CODE_INVALID_RESET_PASSWORD_LINK_TOKEN    = 10;
    const CODE_ADDRESS_NOT_FOUND                    = 11;
    const CODE_INVALID_ADDRESS_ID                   = 12;
    const CODE_VALIDATION_FAILED                    = 13;
    const CODE_INVALID_CUSTOMER_ID                  = 14;

    // Constants for the type of new account email to be sent
    const NEW_ACCOUNT_EMAIL_REGISTERED = 'registered';     // welcome email, when confirmation is disabled
    const NEW_ACCOUNT_EMAIL_CONFIRMED = 'confirmed';       // welcome email, when confirmation is enabled
    const NEW_ACCOUNT_EMAIL_CONFIRMATION = 'confirmation'; // email with confirmation link

    /**
     * Retrieve all Customer Addresses
     *
     * @param int $customerId,
     * @return Entity\V1\Address[]
     * @throws Entity\V1\Exception
     */
    public function getAddresses($customerId);

    /**
     * Retrieve default billing address
     *
     * @param int $customerId
     * @return Entity\V1\Address
     * @throws Entity\V1\Exception
     */
    public function getDefaultBillingAddress($customerId);

    /**
     * Retrieve default shipping address
     *
     * @param int $customerId
     * @return Entity\V1\Address
     * @throws Entity\V1\Exception
     */
    public function getDefaultShippingAddress($customerId);

    /**
     * Retrieve address by id
     *
     * @param int $customerId
     * @param int $addressId
     * @return Entity\V1\Address
     * @throws Entity\V1\Exception
     */
    public function getAddressById($customerId, $addressId);

    /**
     * Removes an address by id.
     *
     * @param int $customerId
     * @param int $addressId
     * @throws Entity\V1\Exception if the address does not belong to the given customer
     */
    public function deleteAddressFromCustomer($customerId, $addressId);

    /**
     * Retrieve Customer Addresses EAV attribute metadata
     *
     * @param string $attributeCode
     * @return Entity\V1\Eav\AttributeMetadata
     */
    public function getAddressAttributeMetadata($attributeCode);

    /**
     * Returns all attribute metadata for Addresses
     *
     * @return Entity\V1\Eav\AttributeMetadata[]
     */
    public function getAllAddressAttributeMetadata();

    /**
     * Insert and/or update a list of addresses.
     *
     * This will add the addresses to the provided customerId.
     * Only one address can be the default billing or shipping
     * so if more than one is set, or if one was already set
     * then the last address in this provided list will take
     * over as the new default.
     *
     * This doesn't support partial updates to addresses, meaning
     * that a full set of data must be provided with each Address
     *
     * @param int                 $customerId
     * @param \Magento\Customer\Service\Entity\V1\Address[] $addresses
     *
     * @throws Entity\V1\AggregateException if there are validation errors.
     * @throws Entity\V1\Exception If customerId is not found or other error occurs.
     * @return int[] address ids
     */
    public function saveAddresses($customerId, array $addresses);

    /**
     * Retrieve Customer
     *
     * @param int $customerId
     * @return Entity\V1\Customer
     */
    public function getCustomer($customerId);

    /**
     * Retrieve Customer EAV attribute metadata
     *
     * @param string $attributeCode
     * @return Entity\V1\Eav\AttributeMetadata
     */
    public function getCustomerAttributeMetadata($attributeCode);

    /**
     * Returns all attribute metadata for customers
     *
     * @return Entity\V1\Eav\AttributeMetadata[]
     */
    public function getAllCustomerAttributeMetadata();

    /**
     * Used to activate a customer account using a key that was sent in a confirmation e-mail.
     *
     * @param int $customerId
     * @param string $key
     * @throws Entity\V1\Exception If customerId is invalid, does not exist, or customer account was already active
     * @throws \Magento\Core\Exception If there is an issue with supplied $customerId or $key
     * @return Entity\V1\Customer
     */
    public function activateAccount($customerId, $key);

    /**
     * Login a customer account using username and password
     *
     * @param string $username username in plain-text
     * @param string $password password in plain-text
     * @throws Entity\V1\Exception if unable to login due to issue with username or password or others
     * @return Entity\V1\Customer
     */
    public function authenticate($username, $password);

    /**
     * Create or update customer information
     *
     * @param \Magento\Customer\Service\Entity\V1\Customer $customer
     * @param string $password
     * @throws Entity\V1\Exception
     * @return int customer ID
     */
    public function saveCustomer(Entity\V1\Customer $customer, $password = null);

    /**
     * Check if password reset token is valid
     *
     * @param int $customerId
     * @param string $resetPasswordLinkToken
     * @throws Entity\V1\Exception if expired or invalid
     */
    public function validateResetPasswordLinkToken($customerId, $resetPasswordLinkToken);

    /**
     * Send an email to the customer with a password reset link.
     *
     * @param string $email
     * @param int $websiteId
     * @throws Entity\V1\Exception
     */
    public function sendPasswordResetLink($email, $websiteId);


    /**
     * Reset customer password.
     *
     * @param int $customerId
     * @param string $password
     * @param string $resetToken
     */
    public function resetPassword($customerId, $password, $resetToken);

    /**
     * Create Customer Account
     *
     * @param \Magento\Customer\Service\Entity\V1\Customer $customer
     * @param \Magento\Customer\Service\Entity\V1\Address[] $addresses
     * @param string $password
     * @param string $confirmationBackUrl
     * @param string $registeredBackUrl
     * @param int $storeId
     * @return Entity\V1\Response\CreateCustomerAccountResponse
     */
    public function createAccount(
        Entity\V1\Customer $customer,
        array $addresses,
        $password = null,
        $confirmationBackUrl = '',
        $registeredBackUrl = '',
        $storeId = 0
    );

    /*
     * Send Confirmation email
     *
     * @param string $email email address of customer
     * @throws Entity\V1\Exception if error occurs getting customerId
     */
    public function sendConfirmation($email);

    /**
     * Validate customer entity
     *
     * @param \Magento\Customer\Service\Entity\V1\Customer $customer
     * @param \Magento\Customer\Service\Entity\V1\Eav\AttributeMetadata[] $attributes
     * @return array|bool
     */
    public function validateCustomerData(Entity\V1\Customer $customer, array $attributes);
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

/**
 * Manipulate Customer Address Entities *
 */
interface CustomerAccountServiceInterface
{
    /** account response status */
    const ACCOUNT_CONFIRMATION = "confirmation";
    const ACCOUNT_REGISTERED = "registered";

    // Constants for the type of new account email to be sent
    const NEW_ACCOUNT_EMAIL_REGISTERED = 'registered';     // welcome email, when confirmation is disabled
    const NEW_ACCOUNT_EMAIL_CONFIRMED = 'confirmed';       // welcome email, when confirmation is enabled
    const NEW_ACCOUNT_EMAIL_CONFIRMATION = 'confirmation'; // email with confirmation link

    /**
     * Create Customer Account
     *
     * @param Dto\Customer $customer
     * @param Dto\Address[] $addresses
     * @param string $password
     * @param string $confirmationBackUrl
     * @param string $registeredBackUrl
     * @param int $storeId
     * @return Dto\Response\CreateCustomerAccountResponse
     */
    public function createAccount(
        Dto\Customer $customer,
        array $addresses,
        $password = null,
        $confirmationBackUrl = '',
        $registeredBackUrl = '',
        $storeId = 0
    );

    /**
     * Used to activate a customer account using a key that was sent in a confirmation e-mail.
     *
     * @param int $customerId
     * @param string $key
     * @throws \Magento\Exception\InputException If customerId is invalid, key is invalid
     * @throws \Magento\Exception\StateException
     *      StateException::INPUT_MISMATCH if key doesn't match expected.
     *      StateException::INVALID_STATE_CHANGE if account already active.
     * @return Dto\Customer
     */
    public function activateAccount($customerId, $key);

    /**
     * Login a customer account using username and password
     *
     * @param string $username username in plain-text
     * @param string $password password in plain-text
     * @throws \Magento\Exception\AuthenticationException if unable to authenticate
     * @return Dto\Customer
     */
    public function authenticate($username, $password);

    /**
     * Check if password reset token is valid
     *
     * @param int $customerId
     * @param string $resetPasswordLinkToken
     * @throws \Magento\Exception\StateException if token is expired or mismatched
     * @throws \Magento\Exception\InputException if token or customer id is invalid
     * @throws \Magento\Exception\NoSuchEntityException if customer doesn't exist
     */
    public function validateResetPasswordLinkToken($customerId, $resetPasswordLinkToken);

    /**
     * Send an email to the customer with a password reset link.
     *
     * @param string $email
     * @param int $websiteId
     * @throws \Magento\Exception\NoSuchEntityException
     */
    public function sendPasswordResetLink($email, $websiteId);


    /**
     * Reset customer password.
     *
     * @param int $customerId
     * @param string $password
     * @param string $resetToken
     * @throws \Magento\Exception\StateException if token is expired or mismatched
     * @throws \Magento\Exception\InputException if token or customer id is invalid
     * @throws \Magento\Exception\NoSuchEntityException if customer doesn't exist
     */
    public function resetPassword($customerId, $password, $resetToken);

    /*
     * Send Confirmation email
     *
     * @param string $email email address of customer
     * @throws \Magento\Exception\NoSuchEntityException if no customer found for provided email
     * @throws \Magento\Exception\StateException if confirmation is not needed
     */
    public function sendConfirmation($email);

    /**
     * Validate customer entity
     *
     * @param Dto\Customer $customer
     * @param Dto\Eav\AttributeMetadata[] $attributes
     * @return array|bool
     */
    public function validateCustomerData(Dto\Customer $customer, array $attributes);

}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1;

/**
 * Interface CustomerAccountServiceInterface
 */
interface CustomerAccountServiceInterface
{
    /** account response status @deprecated */
    const ACCOUNT_CONFIRMATION = "confirmation";

    const ACCOUNT_REGISTERED = "registered";

    // Constants for the type of new account email to be sent
    const NEW_ACCOUNT_EMAIL_REGISTERED = 'registered';

    // welcome email, when confirmation is disabled
    const NEW_ACCOUNT_EMAIL_CONFIRMED = 'confirmed';

    // welcome email, when confirmation is enabled
    const NEW_ACCOUNT_EMAIL_CONFIRMATION = 'confirmation';

    // email with confirmation link

    // Constants for confirmation statuses
    const ACCOUNT_CONFIRMED = 'account_confirmed';

    const ACCOUNT_CONFIRMATION_REQUIRED = 'account_confirmation_required';

    const ACCOUNT_CONFIRMATION_NOT_REQUIRED = 'account_confirmation_not_required';

    /**
     * Constants for types of emails to send out.
     * pdl:
     * forgot, remind, reset email templates
     */
    const EMAIL_REMINDER = 'email_reminder';

    const EMAIL_RESET = 'email_reset';

    /**
     * Create Customer Account
     *
     * @param \Magento\Customer\Service\V1\Data\CustomerDetails $customerDetails
     * @param string $password If null then a random password will be assigned. Disregard if $hash is not empty.
     * @param string $hash Password hash that we can save directly
     * @param string $redirectUrl URL fed to welcome email templates. Can be used by templates to, for example, direct
     *                            the customer to a product they were looking at after pressing confirmation link.
     * @return \Magento\Customer\Service\V1\Data\Customer
     * @throws \Exception If something goes wrong during save
     * @throws \Magento\Exception\InputException If bad input is provided
     * @throws \Magento\Exception\StateException If the provided email is already used
     */
    public function createAccount(
        \Magento\Customer\Service\V1\Data\CustomerDetails $customerDetails,
        $password = null,
        $hash = null,
        $redirectUrl = ''
    );

    /**
     * Update Customer Account and its details.
     * CustomerDetails contains an array of Address Data. In the event that no change was made to addresses
     * the array must be null.
     *
     * @param \Magento\Customer\Service\V1\Data\CustomerDetails $customerDetails
     * @throws \Magento\Exception\NoSuchEntityException If customer with customerDetails is not found.
     * @return bool True if this customer was updated
     */
    public function updateCustomer(\Magento\Customer\Service\V1\Data\CustomerDetails $customerDetails);

    /**
     * Create or update customer information
     *
     * @param \Magento\Customer\Service\V1\Data\Customer $customer
     * @param string $password Plain text password
     * @param string $hash Hashed password ready to be saved
     * @throws \Magento\Customer\Exception If something goes wrong during save
     * @throws \Magento\Exception\InputException If bad input is provided
     * @return int customer ID
     * @deprecated use createAccount or updateCustomer instead
     */
    public function saveCustomer(\Magento\Customer\Service\V1\Data\Customer $customer, $password = null, $hash = null);

    /**
     * Retrieve Customer
     *
     * @param int $customerId
     * @throws \Magento\Exception\NoSuchEntityException If customer with customerId is not found.
     * @return \Magento\Customer\Service\V1\Data\Customer
     */
    public function getCustomer($customerId);

    /**
     * Used to activate a customer account using a key that was sent in a confirmation e-mail.
     *
     * @param int $customerId
     * @param string $confirmationKey Sent to customer in an confirmation e-mail.
     * @return \Magento\Customer\Service\V1\Data\Customer
     * @throws \Magento\Exception\NoSuchEntityException If customer doesn't exist
     * @throws \Magento\Exception\StateException
     *      StateException::INPUT_MISMATCH if key doesn't match expected.
     *      StateException::INVALID_STATE if account already active.
     */
    public function activateCustomer($customerId, $confirmationKey);

    /**
     * Retrieve customers which match a specified criteria
     *
     * @param \Magento\Customer\Service\V1\Data\SearchCriteria $searchCriteria
     * @throws \Magento\Exception\InputException if there is a problem with the input
     * @return \Magento\Customer\Service\V1\Data\SearchResults containing Data\CustomerDetails
     */
    public function searchCustomers(\Magento\Customer\Service\V1\Data\SearchCriteria $searchCriteria);

    /**
     * Login a customer account using username and password
     *
     * @param string $username username in plain-text
     * @param string $password password in plain-text
     * @return \Magento\Customer\Service\V1\Data\Customer
     * @throws \Magento\Exception\AuthenticationException If unable to authenticate
     */
    public function authenticate($username, $password);

    /**
     * Change customer password.
     *
     * @param int $customerId
     * @param string $currentPassword
     * @param string $newPassword
     * @return void
     * @throws \Magento\Exception\NoSuchEntityException If customer with customerId is not found.
     * @throws \Magento\Exception\AuthenticationException If invalid currentPassword is supplied
     */
    public function changePassword($customerId, $currentPassword, $newPassword);

    /**
     * Return hashed password, which can be directly saved to database.
     *
     * @param string $password
     * @return string
     * @todo this method has to be removed when the checkout process refactored in the way it won't require to pass
     *       a password through requests
     */
    public function getPasswordHash($password);

    /**
     * Check if password reset token is valid
     *
     * @param int $customerId
     * @param string $resetPasswordLinkToken
     * @return void
     * @throws \Magento\Exception\StateException If token is expired or mismatched
     * @throws \Magento\Exception\InputException If token or customer id is invalid
     * @throws \Magento\Exception\NoSuchEntityException If customer doesn't exist
     */
    public function validateResetPasswordLinkToken($customerId, $resetPasswordLinkToken);

    /**
     * Send an email to the customer with a password reset link.
     *
     * @param string $email
     * @param int $websiteId
     * @param string $template Type of email to send.  Must be one of the email constants.
     * @return void
     * @throws \Magento\Exception\NoSuchEntityException
     */
    public function initiatePasswordReset($email, $websiteId, $template);

    /**
     * Reset customer password.
     *
     * @param int $customerId
     * @param string $resetToken Token sent to customer via e-mail
     * @param string $newPassword
     * @return void
     * @throws \Magento\Exception\StateException If token is expired or mismatched
     * @throws \Magento\Exception\InputException If token or customer id is invalid
     * @throws \Magento\Exception\NoSuchEntityException If customer doesn't exist
     */
    public function resetPassword($customerId, $resetToken, $newPassword);

    /**
     * Gets the account confirmation status
     *
     * @param int $customerId
     * @return string returns one of the account confirmation statuses
     *
     * @throws \Magento\Exception\NoSuchEntityException If customer with customerId is not found.
     */
    public function getConfirmationStatus($customerId);

    /**
     * Resend confirmation email.
     *
     * @param string $email email address of customer
     * @param string $websiteId website
     * @param string $redirectUrl URL fed to welcome email templates. Can be used by templates to, for example, direct
     *                            the customer to a product they were looking at after pressing confirmation link.
     * @return void
     * @throws \Magento\Exception\NoSuchEntityException If no customer found for provided email
     * @throws \Magento\Exception\StateException If confirmation is not needed
     */
    public function resendConfirmation($email, $websiteId, $redirectUrl = '');

    /**
     * Validate customer entity
     *
     * @param \Magento\Customer\Service\V1\Data\Customer $customer
     * @param \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata[] $attributes
     * @return \Magento\Customer\Service\V1\Data\CustomerValidationResults
     */
    public function validateCustomerData(
        \Magento\Customer\Service\V1\Data\Customer $customer,
        array $attributes = array()
    );

    /**
     * Indicates if the Customer for the provided customerId is restricted to being read only
     * for the currently logged in user, or if modifications can be made.
     *
     * @param int $customerId
     * @throws \Magento\Exception\NoSuchEntityException If customer with customerId is not found.
     * @return bool true if modifications can be made; false if read only.
     */
    public function canModify($customerId);

    /**
     * Indicates if the Customer for the currently logged in user as specified by the provided
     * customerId can be deleted.
     *
     * @param int $customerId
     * @throws \Magento\Exception\NoSuchEntityException If customer with customerId is not found.
     * @return bool true if the customer can be deleted
     */
    public function canDelete($customerId);

    /**
     * Retrieve customer details
     *
     * @param int $customerId
     * @throws \Magento\Exception\NoSuchEntityException If customer with customerId is not found.
     * @return \Magento\Customer\Service\V1\Data\CustomerDetails
     */
    public function getCustomerDetails($customerId);

    /**
     * Delete Customer
     *
     * @param int $customerId
     * @throws \Magento\Customer\Exception If something goes wrong during delete
     * @throws \Magento\Exception\NoSuchEntityException If customer with customerId is not found.
     * @return bool True if the customer was deleted
     */
    public function deleteCustomer($customerId);

    /**
     * Check if the email has not been associated with a customer account in given website
     *
     * @param string $customerEmail
     * @param int $websiteId
     * @return bool true if the email is not associated with a customer account in given website
     * @throws \Magento\Exception\NoSuchEntityException If customer with email is not found
     * @throws \Magento\Model\Exception If website was not specified
     */
    public function isEmailAvailable($customerEmail, $websiteId);

    /**
     * Check store availability for customer given the customerId
     *
     * @param int $customerWebsiteId
     * @param int $storeId
     * @return bool
     */
    public function isCustomerInStore($customerWebsiteId, $storeId);

    /**
     * Check if the email has not been associated with a customer account in given website
     *
     * @param string $customerEmail
     * @param int $websiteId If not set, will use the current websiteId
     * @throws \Magento\Exception\NoSuchEntityException If customer with customerEmail is not found.
     * @return \Magento\Customer\Service\V1\Data\Customer
     */
    public function getCustomerByEmail($customerEmail, $websiteId = null);
}

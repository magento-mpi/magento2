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
     * @param \Magento\Customer\Service\V1\Dto\Customer $customer
     * @param \Magento\Customer\Service\V1\Dto\Address[] $addresses
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
     * @throws \Magento\Customer\Service\Entity\V1\Exception If customerId is invalid, does not exist, or customer account was already active
     * @throws \Magento\Core\Exception If there is an issue with supplied $customerId or $key
     * @return \Magento\Customer\Service\V1\Dto\Customer
     */
    public function activateAccount($customerId, $key);

    /**
     * Login a customer account using username and password
     *
     * @param string $username username in plain-text
     * @param string $password password in plain-text
     * @throws \Magento\Customer\Service\Entity\V1\Exception if unable to login due to issue with username or password or others
     * @return \Magento\Customer\Service\V1\Dto\Customer
     */
    public function authenticate($username, $password);

    /**
     * Check if password reset token is valid
     *
     * @param int $customerId
     * @param string $resetPasswordLinkToken
     * @throws \Magento\Customer\Service\Entity\V1\Exception if expired or invalid
     */
    public function validateResetPasswordLinkToken($customerId, $resetPasswordLinkToken);

    /**
     * Send an email to the customer with a password reset link.
     *
     * @param string $email
     * @param int $websiteId
     * @throws \Magento\Customer\Service\Entity\V1\Exception
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
     * @param \Magento\Customer\Service\V1\Dto\Customer $customer
     * @param \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata[] $attributes
     * @return array|bool
     */
    public function validateCustomerData(Dto\Customer $customer, array $attributes);

}

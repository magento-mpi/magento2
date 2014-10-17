<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Api;

/**
 * Interface for managing customers accounts.
 */
interface AccountManagement
{
    const DEFAULT_PASSWORD_LENGTH = 6;

    /**
     * Constants for the type of new account email to be sent
     */
    const NEW_ACCOUNT_EMAIL_REGISTERED = 'registered';

    /**
     * Welcome email, when confirmation is enabled
     */
    const NEW_ACCOUNT_EMAIL_CONFIRMATION = 'confirmation';

    /**
     * Create customer account. Perform necessary business operations like sending email.
     *
     * @param \Magento\Customer\Api\Data\Customer $customer
     * @param string $password
     * @param string $redirectUrl
     * @return \Magento\Customer\Api\Data\Customer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createAccount(\Magento\Customer\Api\Data\Customer $customer, $password, $redirectUrl = '' );

    /**
     * Validate customer data.
     *
     * @param \Magento\Customer\Api\Data\Customer $customer
     * @return \Magento\Customer\Api\Data\ValidationResults
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate(\Magento\Customer\Api\Data\Customer $customer);

    /**
     * Check if customer can be deleted.
     *
     * @param int $customerId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException If group is not found
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isReadonly($customerId);

    /**
     * Activate a customer account using a key that was sent in a confirmation e-mail.
     *
     * @param string $email
     * @param string $confirmationKey
     * @return \Magento\Customer\Api\Data\Customer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function activate($email, $confirmationKey);

    /**
     * Authenticate a customer by username and password
     *
     * @param string $email
     * @param string $password
     * @return \Magento\Customer\Api\Data\Customer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function authenticate($email, $password);

    /**
     * Change customer password.
     *
     * @param string $email
     * @param string $currentPassword
     * @param string $newPassword
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function changePassword($email, $currentPassword, $newPassword);

    /**
     * Send an email to the customer with a password reset link.
     *
     * @param string $email
     * @param string $template
     * @param string $websiteId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function initiatePasswordReset($email, $template, $websiteId = null);

    /**
     * Reset customer password.
     *
     * @param string $email
     * @param string $resetToken
     * @param string $newPassword
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function resetPassword($email, $resetToken, $newPassword);

    /**
     * Gets the account confirmation status.
     *
     * @param string $email
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getConfirmationStatus($email);

    /**
     * Resend confirmation email.
     *
     * @param string $email
     * @param string $websiteId
     * @param string $redirectUrl
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function resendConfirmation($email, $websiteId, $redirectUrl = '');

    /**
     * Check if given email is associated with a customer account in given website.
     *
     * @param string $customerEmail
     * @param int $websiteId If not set, will use the current websiteId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isEmailAvailable($customerEmail, $websiteId = null);
}

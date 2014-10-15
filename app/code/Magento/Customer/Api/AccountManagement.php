<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Api;

interface AccountManagement
{
    /**
     * @param int $customerId
     * @return bool
     */
    public function isReadonly($customerId);

    /**
     * Used to activate a customer account using a key that was sent in a confirmation e-mail.
     *
     * @param string $email
     * @param string $confirmationKey
     * @return \Magento\Customer\Api\Data\Customer
     */
    public function activate($email, $confirmationKey);

    /**
     * Login a customer account using username and password
     *
     * @param string $email
     * @param string $password
     * @return \Magento\Customer\Api\Data\Customer
     */
    public function authenticate($email, $password);

    /**
     * Change customer password.
     *
     * @param string $email
     * @param string $currentPassword
     * @param string $newPassword
     */
    public function changePassword($email, $currentPassword, $newPassword);

    /**
     * Send an email to the customer with a password reset link.
     *
     * @param string $email
     * @param string $template
     * @param string $websiteId
     */
    public function initiatePasswordReset($email, $template, $websiteId = null);

    /**
     * Reset customer password.
     *
     * @param string $email
     * @param string $resetToken
     * @param string $newPassword
     */
    public function resetPassword($email, $resetToken, $newPassword);

    /**
     * Gets the account confirmation status.
     *
     * @param string $email
     */
    public function getConfirmationStatus($email);

    /**
     * Resend confirmation email.
     *
     * @param string $email
     * @param string $websiteId
     * @param string $redirectUrl
     */
    public function resendConfirmation($email, $websiteId, $redirectUrl = '');
}

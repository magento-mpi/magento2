<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Api;

interface AccountRepository
{
    const DEFAULT_PASSWORD_LENGTH = 6;

    /**
     * Constants for the type of new account email to be sent
     */
    const NEW_ACCOUNT_EMAIL_REGISTERED = 'registered';

    /**
     * welcome email, when confirmation is enabled
     */
    const NEW_ACCOUNT_EMAIL_CONFIRMATION = 'confirmation';

    /**
     * @param \Magento\Customer\Api\Data\Account $account
     * @param string $redirectUrl
     * @return \Magento\Customer\Api\Data\Account
     */
    public function persist(
        \Magento\Customer\Api\Data\Account $account,
        $redirectUrl = '' /* remove, find alternative way */
    );

    /**
     * @param \Magento\Customer\Api\Data\Account $account
     * @return bool True if the customer was deleted
     */
    public function update(\Magento\Customer\Api\Data\Account $account);

    /**
     * @param string $email
     * @param int $websiteId
     * @return \Magento\Customer\Api\Data\Customer
     */
    public function get($email, $websiteId);

    /**
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return mixed
     */
    public function getList(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);
} 

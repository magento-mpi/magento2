<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Api;

interface CustomerEmailList
{
    /**
     * Check if the email has not been associated with a customer account in given website
     *
     * @param string $customerEmail
     * @param int $websiteId If not set, will use the current websiteId
     * @return bool true if the email is not associated with a customer account in given website
     */
    public function isEmailAvailable($customerEmail, $websiteId = null);
}

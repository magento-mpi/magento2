<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Service\V1;


interface TokenInterface
{
    /**
     * Create access token for admin given the admin id.
     *
     * @param int $userId
     * @return string Token created
     */
    public function createAdminAccessToken($userId);

    /**
     * Create access token for customer given the customer id.
     *
     * @param int $userId
     * @return string Token created
     */
    public function createCustomerAccessToken($userId);

} 

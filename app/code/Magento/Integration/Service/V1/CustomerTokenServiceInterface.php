<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Service\V1;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\InputException;

/**
 * Interface providing token generation for Customers
 */
interface CustomerTokenServiceInterface
{
    /**
     * Create access token for admin given the customer credentials.
     *
     * @param string $username
     * @param string $password
     * @return string Token created
     * @throws InputException For invalid input
     * @throws AuthenticationException
     */
    public function createCustomerAccessToken($username, $password);
}
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
use Magento\Framework\Exception\LocalizedException;

/**
 * Interface TokenServiceInterface
 */
interface TokenServiceInterface
{
    /**
     * Create access token for admin given the admin credentials.
     *
     * @param string $username
     * @param string $password
     * @return string Token created
     * @throws InputException For invalid input
     * @throws AuthenticationException
     * @throws LocalizedException
     */
    public function createAdminAccessToken($username, $password);

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

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Service\V1;


use Magento\Framework\Exception\AuthenticationException;

interface LoginServiceInterface
{
    /**
     * Login registered users and initiate a session
     *
     * @param string $username
     * @param string $password
     * @return string|null Session id if user is authenticated else null
     * @throws AuthenticationException
     */
    public function login($username, $password);

    /**
     * Initiate a session for unregistered users
     *
     * @return string Session id
     * @throws AuthenticationException
     */
    public function loginAnonymous();
} 
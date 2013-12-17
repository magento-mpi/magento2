<?php
/**
 * Magento session manager interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Session;

/**
 * Session Manager Interface
 */
interface SessionManagerInterface
{
    /**
     * Session key for list of hosts
     */
    const HOST_KEY = '_session_hosts';

    /**
     * Start session
     *
     * @param string $sessionName
     * @return SessionManagerInterface
     */
    public function start($sessionName = null);

    /**
     * Session write close
     */
    public function writeClose();

    /**
     * Does a session exist
     *
     * @return bool
     */
    public function isSessionExists();

    /**
     * Retrieve session Id
     *
     * @return string
     */
    public function getSessionId();

    /**
     * Retrieve session name
     *
     * @return string
     */
    public function getName();

    /**
     * Set session name
     *
     * @param string $name
     * @return SessionManagerInterface
     */
    public function setName($name);

    /**
     * Destroy/end a session
     *
     * @param  array $options
     */
    public function destroy(array $options = null);

    /**
     * Unset session data
     *
     * @return $this
     */
    public function clearStorage();

    /**
     * Retrieve Cookie domain
     *
     * @return string
     */
    public function getCookieDomain();

    /**
     * Retrieve cookie path
     *
     * @return string
     */
    public function getCookiePath();

    /**
     * Retrieve cookie lifetime
     *
     * @return int
     */
    public function getCookieLifetime();

    /**
     * Specify session identifier
     *
     * @param string|null $sessionId
     * @return SessionManagerInterface
     */
    public function setSessionId($sessionId);

    /**
     * Renew session id and update session cookie
     *
     * @param bool $deleteOldSession
     * @return SessionManagerInterface
     */
    public function regenerateId($deleteOldSession = true);

    /**
     * Expire the session cookie
     *
     * Sends a session cookie with no value, and with an expiry in the past.
     */
    public function expireSessionCookie();

    /**
     * If session cookie is not applicable due to host or path mismatch - add session id to query
     *
     * @param string $urlHost
     * @return string
     */
    public function getSessionIdForHost($urlHost);

    /**
     * Check if session is valid for given hostname
     *
     * @param string $host
     * @return bool
     */
    public function isValidForHost($host);

    /**
     * Check if session is valid for given path
     *
     * @param string $path
     * @return bool
     */
    public function isValidForPath($path);
}

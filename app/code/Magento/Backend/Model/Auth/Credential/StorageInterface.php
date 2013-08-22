<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Backend Auth Credential Storage interface
 *
 * @category    Magento
 * @package     Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Magento_Backend_Model_Auth_Credential_StorageInterface
{
    /**
     * Authenticate process.
     *
     * @abstract
     * @param string $username
     * @param string $password
     */
    public function authenticate($username, $password);

    /**
     * Login action. Check if given username and password are valid
     *
     * @abstract
     * @param $username
     * @param $password
     */
    public function login($username, $password);

    /**
     * Reload loaded (already authenticated) credential storage
     *
     * @abstract
     */
    public function reload();

    /**
     * Check if user has available resources
     *
     * @abstract
     * @return bool
     */
    public function hasAvailableResources();

    /**
     * Set user has available resources
     *
     * @abstract
     * @param bool $hasResources
     */
    public function setHasAvailableResources($hasResources);
}

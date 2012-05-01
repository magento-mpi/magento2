<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Backend Auth Credential Storage interface
 *
 * @category    Mage
 * @package     Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Backend_Model_Auth_Credential_StorageInterface
{
    /**
     * Authenticate process.
     *
     * @abstract
     * @param string$username
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
}

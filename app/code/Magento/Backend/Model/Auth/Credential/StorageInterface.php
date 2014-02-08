<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Auth\Credential;

/**
 * Backend Auth Credential Storage interface
 *
 * @category    Magento
 * @package     Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface StorageInterface
{
    /**
     * Authenticate process.
     *
     * @param string $username
     * @param string $password
     * @return bool
     * @abstract
     */
    public function authenticate($username, $password);

    /**
     * Login action. Check if given username and password are valid
     *
     * @param $username
     * @param $password
     * @return $this
     * @abstract
     */
    public function login($username, $password);

    /**
     * Reload loaded (already authenticated) credential storage
     *
     * @return $this
     * @abstract
     */
    public function reload();

    /**
     * Check if user has available resources
     *
     * @return bool
     * @abstract
     */
    public function hasAvailableResources();

    /**
     * Set user has available resources
     *
     * @param bool $hasResources
     * @return $this
     * @abstract
     */
    public function setHasAvailableResources($hasResources);
}

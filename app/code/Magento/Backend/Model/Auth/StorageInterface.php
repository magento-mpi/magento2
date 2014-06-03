<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Auth;

/**
 * Backend Auth Storage interface
 */
interface StorageInterface
{
    /**
     * Perform login specific actions
     *
     * @return $this
     * @abstract
     */
    public function processLogin();

    /**
     * Perform login specific actions
     *
     * @return $this
     * @abstract
     */
    public function processLogout();

    /**
     * Check if user is logged in
     *
     * @return bool
     * @abstract
     */
    public function isLoggedIn();

    /**
     * Prolong storage lifetime
     *
     * @return void
     * @abstract
     */
    public function prolong();
}

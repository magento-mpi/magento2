<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
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
}

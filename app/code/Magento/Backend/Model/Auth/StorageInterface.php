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
 * Backend Auth Storage interface
 *
 * @category    Magento
 * @package     Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Auth;

interface StorageInterface
{
    /**
     * Perform login specific actions
     *
     * @abstract
     */
    public function processLogin();

    /**
     * Perform login specific actions
     *
     * @abstract
     */
    public function processLogout();
}

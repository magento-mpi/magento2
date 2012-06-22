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
 * Backend Auth Storage interface
 *
 * @category    Mage
 * @package     Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Backend_Model_Auth_StorageInterface
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

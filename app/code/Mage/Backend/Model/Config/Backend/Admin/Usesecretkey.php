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
 * Config backend model for "Use secret key in Urls" option
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Backend_Admin_Usesecretkey extends Magento_Core_Model_Config_Data
{
    protected function _afterSave()
    {
        Mage::getSingleton('Mage_Backend_Model_Url')->renewSecretUrls();
        return $this;
    }
}

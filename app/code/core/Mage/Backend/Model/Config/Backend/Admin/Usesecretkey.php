<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml backend model for "Use secret key in Urls" option
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Backend_Admin_Usesecretkey extends Mage_Core_Model_Config_Data
{
    protected function _afterSave()
    {
        Mage::getSingleton('Mage_Adminhtml_Model_Url')->renewSecretUrls();
        return $this;
    }
}

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
 * Backend add store code to url backend
 *
 * @category    Mage
 * @package     Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Backend_Store extends Magento_Core_Model_Config_Data
{
    protected function _afterSave()
    {
        Mage::app()->getStore()->setConfig(Magento_Core_Model_Store::XML_PATH_STORE_IN_URL, $this->getValue());
        Mage::app()->cleanCache();
    }
}

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
 * Backend add store code to url backend
 *
 * @category    Magento
 * @package     Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Model_Config_Backend_Store extends Magento_Core_Model_Config_Value
{
    protected function _afterSave()
    {
        Mage::app()->getStore()->setConfig(Magento_Core_Model_Store::XML_PATH_STORE_IN_URL, $this->getValue());
        Mage::app()->cleanCache();
    }
}

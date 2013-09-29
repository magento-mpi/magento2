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
 */
class Magento_Backend_Model_Config_Backend_Store extends Magento_Core_Model_Config_Value
{
    protected function _afterSave()
    {
        $this->_storeManager->getStore()->setConfig(Magento_Core_Model_Store::XML_PATH_STORE_IN_URL, $this->getValue());
        $this->_cacheManager->clean();
    }
}

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
namespace Magento\Backend\Model\Config\Backend;

class Store extends \Magento\Core\Model\Config\Value
{
    protected function _afterSave()
    {
        $this->_storeManager->getStore()->setConfig(\Magento\Core\Model\Store::XML_PATH_STORE_IN_URL, $this->getValue());
        $this->_cacheManager->clean();
    }
}

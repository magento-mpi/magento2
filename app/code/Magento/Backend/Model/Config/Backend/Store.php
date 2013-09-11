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
namespace Magento\Backend\Model\Config\Backend;

class Store extends \Magento\Core\Model\Config\Value
{
    protected function _afterSave()
    {
        \Mage::app()->getStore()->setConfig(\Magento\Core\Model\Store::XML_PATH_STORE_IN_URL, $this->getValue());
        \Mage::app()->cleanCache();
    }
}

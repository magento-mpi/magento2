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
 * Config backend model for "Use Custom Admin Path" option
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Model_Config_Backend_Admin_Usecustompath extends Magento_Core_Model_Config_Value
{
    /**
     * Check whether redirect should be set
     *
     * @return Magento_Backend_Model_Config_Backend_Admin_Usecustompath
     */
    protected function _beforeSave()
    {
        if ($this->getOldValue() != $this->getValue()) {
            Mage::helper('Magento_Backend_Helper_Data')->clearAreaFrontName();
            Mage::register('custom_admin_path_redirect', true, true);
        }

        return $this;
    }
}

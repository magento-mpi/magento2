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
 * Adminhtml backend model for "Custom Admin Path" option
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_System_Config_Backend_Admin_Custompath extends Mage_Core_Model_Config_Data
{
    /**
     * Check whether redirect should be set
     *
     * @return Mage_Adminhtml_Model_System_Config_Backend_Admin_Custom
     */
    protected function _beforeSave()
    {
        if ($this->getOldValue() != $this->getValue()) {
            Mage::helper('Mage_Backend_Helper_Data')->clearAreaFrontName();
            Mage::register('custom_admin_path_redirect', true, true);
        }
        return $this;
    }
}

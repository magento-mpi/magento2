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
 * Config backend model for "Use Custom Admin Path" option
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Backend_Admin_Usecustompath extends Mage_Core_Model_Config_Data
{
    /**
     * Check whether redirect should be set
     *
     * @return Mage_Backend_Model_Config_Backend_Admin_Usecustompath
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
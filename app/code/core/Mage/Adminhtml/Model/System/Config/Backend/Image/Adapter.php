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
 * System config image field backend model for Zend PDF generator
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_System_Config_Backend_Image_Adapter extends Mage_Core_Model_Config_Data
{
    /**
     * Checks if choosen image adapter available
     *
     * @throws Mage_Core_Exception if some of adapter dipendencies was not loaded
     * @return Mage_Adminhtml_Model_System_Config_Backend_File
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();

        $adapter = Varien_Image_Adapter::factory($value);
        try {
            $adapter->checkDependencies();
        } catch (Exception $e) {
            $message = Mage::helper('Mage_Core_Helper_Data')->__('The specified image adapter cannot be used because of some missed dependencies.');
            throw new Mage_Core_Exception($message);
        }

        return $this;
    }
}

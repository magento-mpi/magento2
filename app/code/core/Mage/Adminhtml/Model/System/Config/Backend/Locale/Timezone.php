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
 * System config email field backend model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_System_Config_Backend_Locale_Timezone extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        if (!in_array($this->getValue(), DateTimeZone::listIdentifiers())) {
            Mage::throwException(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Invalid timezone'));
        }

        return $this;
    }
}

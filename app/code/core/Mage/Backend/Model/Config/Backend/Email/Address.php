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
class Mage_Backend_Model_Config_Backend_Email_Address extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (!Zend_Validate::is($value, 'EmailAddress')) {
            Mage::throwException(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Invalid email address "%s".', $value));
        }
        return $this;
    }
}

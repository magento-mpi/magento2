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
 * System config email sender field backend model
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Backend_Email_Sender extends Mage_Core_Model_Config_Data
{
    /**
     * Check sender name validity
     *
     * @return Mage_Backend_Model_Config_Backend_Email_Sender
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (!preg_match("/^[\S ]+$/", $value)) {
            Mage::throwException(
                Mage::helper('Mage_Backend_Helper_Data')
                    ->__('The sender name "%1" is not valid. Please use only visible characters and spaces.', $value)
            );
        }

        if (strlen($value) > 255) {
            Mage::throwException(
                Mage::helper('Mage_Backend_Helper_Data')
                    ->__('Maximum sender name length is 255. Please correct your settings.')
            );
        }
        return $this;
    }
}

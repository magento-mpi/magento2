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
 * System config email sender field backend model
 */
class Magento_Backend_Model_Config_Backend_Email_Sender extends Magento_Core_Model_Config_Value
{
    /**
     * Check sender name validity
     *
     * @return Magento_Backend_Model_Config_Backend_Email_Sender
     * @throws Magento_Core_Exception
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (!preg_match("/^[\S ]+$/", $value)) {
            throw new Magento_Core_Exception(
                __('The sender name "%1" is not valid. Please use only visible characters and spaces.', $value)
            );
        }

        if (strlen($value) > 255) {
            throw new Magento_Core_Exception(
                __('Maximum sender name length is 255. Please correct your settings.')
            );
        }
        return $this;
    }
}

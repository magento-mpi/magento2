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
 * System config email field backend model
 */
class Magento_Backend_Model_Config_Backend_Email_Address extends Magento_Core_Model_Config_Value
{
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (!Zend_Validate::is($value, 'EmailAddress')) {
            throw new Magento_Core_Exception(
                __('Please correct the email address: "%1".', $value)
            );
        }
        return $this;
    }
}

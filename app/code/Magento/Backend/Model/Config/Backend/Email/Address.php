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
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Model_Config_Backend_Email_Address extends Magento_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (!Zend_Validate::is($value, 'EmailAddress')) {
            Mage::throwException(
                __('Please correct the email address: "%1".', $value)
            );
        }
        return $this;
    }
}

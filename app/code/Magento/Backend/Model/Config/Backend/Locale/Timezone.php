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
class Magento_Backend_Model_Config_Backend_Locale_Timezone extends Magento_Core_Model_Config_Value
{
    protected function _beforeSave()
    {
        if (!in_array($this->getValue(), DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC))) {
            throw new Magento_Core_Exception(__('Please correct the timezone.'));
        }
        return $this;
    }
}

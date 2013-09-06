<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Backend_Serialized extends Magento_Core_Model_Config_Value
{
    protected function _afterLoad()
    {
        if (!is_array($this->getValue())) {
            $value = $this->getValue();
            $this->setValue(empty($value) ? false : unserialize($value));
        }
    }

    protected function _beforeSave()
    {
        if (is_array($this->getValue())) {
            $this->setValue(serialize($this->getValue()));
        }
    }
}

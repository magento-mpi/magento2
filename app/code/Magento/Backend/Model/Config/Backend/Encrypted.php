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
 * Encrypted config field backend model
 *
 * @category   Magento
 * @package    Magento_Backend
 */
class Magento_Backend_Model_Config_Backend_Encrypted extends Magento_Core_Model_Config_Data
{

    /**
     * Decrypt value after loading
     *
     */
    protected function _afterLoad()
    {
        $value = (string)$this->getValue();
        if (!empty($value) && ($decrypted = Mage::helper('Magento_Core_Helper_Data')->decrypt($value))) {
            $this->setValue($decrypted);
        }
    }

    /**
     * Encrypt value before saving
     *
     */
    protected function _beforeSave()
    {
        $value = (string)$this->getValue();
        // don't change value, if an obscured value came
        if (preg_match('/^\*+$/', $this->getValue())) {
            $value = $this->getOldValue();
        }
        if (!empty($value) && ($encrypted = Mage::helper('Magento_Core_Helper_Data')->encrypt($value))) {
            $this->setValue($encrypted);
        }
    }

    /**
     * Get & decrypt old value from configuration
     *
     * @return string
     */
    public function getOldValue()
    {
        return Mage::helper('Magento_Core_Helper_Data')->decrypt(parent::getOldValue());
    }
}

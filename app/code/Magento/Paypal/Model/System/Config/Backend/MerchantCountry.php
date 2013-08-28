<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend model for merchant country. Default country used instead of empty value.
 */
class Magento_Paypal_Model_System_Config_Backend_MerchantCountry extends Magento_Core_Model_Config_Data
{
    /**
     * Substitute empty value with Default country.
     */
    protected function _afterLoad()
    {
        $value = (string)$this->getValue();
        if (empty($value)) {
            if ($this->getWebsite()) {
                $defaultCountry = Mage::app()->getWebsite($this->getWebsite())
                    ->getConfig(Magento_Core_Helper_Data::XML_PATH_DEFAULT_COUNTRY);
            } else {
                $defaultCountry = Mage::helper('Magento_Core_Helper_Data')->getDefaultCountry($this->getStore());
            }
            $this->setValue($defaultCountry);
        }
    }
}

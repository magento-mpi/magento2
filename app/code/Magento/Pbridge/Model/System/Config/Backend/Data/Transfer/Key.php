<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System config data transfer key field backend model
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Pbridge_Model_System_Config_Backend_Data_Transfer_Key extends Magento_Core_Model_Config_Value
{
    /**
     * Checks data transfer key length
     *
     * @return Magento_Pbridge_Model_System_Config_Backend_Data_Transfer_Key
     */
    protected function _beforeSave()
    {
        /**
         * Maximum allowed length is hardcoded because currently we use only CIPHER_RIJNDAEL_256
         * @see Magento_Pci_Model_Encryption::_getCrypt
         */
        if (strlen($this->getValue()) > 32) { // strlen() intentionally, to count bytes rather than characters
            Mage::throwException(__('Maximum data transfer key length is 32. Please correct your settings.'));
        }

        return $this;
    }
}

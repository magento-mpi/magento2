<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System config data transfer key field backend model
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Model_System_Config_Backend_Data_Transfer_Key extends Magento_Core_Model_Config_Data
{
    /**
     * Checks data transfer key length
     *
     * @return Enterprise_Pbridge_Model_System_Config_Backend_Data_Transfer_Key
     */
    protected function _beforeSave()
    {
        /**
         * Maximum allowed length is hardcoded because currently we use only CIPHER_RIJNDAEL_256
         * @see Enterprise_Pci_Model_Encryption::_getCrypt
         */
        if (strlen($this->getValue()) > 32) { // strlen() intentionally, to count bytes rather than characters
            Mage::throwException(__('Maximum data transfer key length is 32. Please correct your settings.'));
        }

        return $this;
    }
}

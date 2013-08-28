<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend model for shipping table rates CSV importing
 *
 * @category   Magento
 * @package    Magento_Shipping
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Shipping_Model_Config_Backend_Tablerate extends Magento_Core_Model_Config_Data
{
    public function _afterSave()
    {
        Mage::getResourceModel('Magento_Shipping_Model_Resource_Carrier_Tablerate')->uploadAndImport($this);
    }
}

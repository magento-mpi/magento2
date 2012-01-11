<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Auto-assign customer group Model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_System_Config_Backend_Customer_GroupAutoAssign extends Mage_Core_Model_Config_Data
{
    /**
     * If merchant country is not in EU, VAT Validation should be disabled
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $storeId = $this->getScopeId();
        $merchantCountry = Mage::getStoreConfig('general/store_information/merchant_country', $storeId);

        if (!Mage::helper('Mage_Core_Helper_Data')->isCountryInEU($merchantCountry, $storeId)) {
            Mage::getConfig()->saveConfig(
                Mage_Customer_Helper_Address::XML_PATH_VAT_VALIDATION_ENABLED,
                0,
                $this->getScope(),
                $storeId
            );
        }

        return parent::_beforeSave();
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config data model
 */
class Saas_UnitPrice_Model_Config_Data_Unitprice_Label extends Magento_Core_Model_Config_Data
{
    /**
     * Prepares frontend label for save
     */
    protected function _beforeSave()
    {
        $data = $this->getValue();
        $this->setValue($this->_getHelper()->stripTags($data));
        return parent::_beforeSave();
    }

    /**
     * Get Mage Helper
     */
    protected function _getHelper()
    {
        return Mage::helper('Magento_Core_Helper_Data');
    }
}

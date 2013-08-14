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
 * Config data backend model for unit selection
 *
 * @category   Saas
 * @package    Saas_UnitPrice
 */
class Saas_UnitPrice_Model_Config_Data_Unitprice_Unit extends Magento_Core_Model_Config_Data
{

    /**
     * Prepare the unit selecton array before saving
     *
     * @return Self
     */
    protected function _beforeSave()
    {

        if (!$this->hasValue()) {
            $this->setValue(array($this->getDefaultValue()));
        } else {
            try {
                $fieldsetData = $this->getFieldsetData();
                $this->_getUnitPrice()->getConversionRate(
                    $fieldsetData['default_unit_price_unit'],
                    $fieldsetData['default_unit_price_base_unit']
                );
            } catch (Exception $e) {
                $this->setValue($this->getOldValue());
            }
        }

        return parent::_beforeSave();
    }

    /**
     * Return the default value for base price unit
     *
     * @return default_unit_price_base_unit value
     */
    public function getDefaultValue()
    {
        return $this->_getHelper()->getConfig('default_unit_price_base_unit');
    }

    /**
     * Returns data helper
     *
     * @return Saas_UnitPrice_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('Saas_UnitPrice_Helper_Data');
    }

    /**
     * Retuns unit price model
     *
     * @return Saas_UnitPrice_Model_Unitprice
     */
    protected function _getUnitPrice()
    {
        return Mage::getSingleton('Saas_UnitPrice_Model_Unitprice');
    }
}

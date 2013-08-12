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
 * Source model for the baseprce Units
 *
 * @category   Saas
 * @package    Saas_UnitPrice
 */
class Saas_UnitPrice_Model_Config_Source_Unitprice_Unit extends Magento_Eav_Model_Entity_Attribute_Source_Abstract
{
    protected $_options;

    /**
     * Returns options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $helper = $this->_getHelper();

        foreach (explode(',', $helper->getConfig('units')) as $unit) {
            $options[] = array('value' => $unit, 'label' => $helper->__($unit));
        }

        return $options;
    }

    /**
     * Returns options
     *
     * @see Magento_Eav_Model_Entity_Attribute_Source_Interface::getAllOptions()
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }

    /**
     * Returns default value
     *
     * @return string
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
     * Returns unit price model
     *
     * @return Saas_UnitPrice_Model_Unitprice
     */
    protected function _getUnitPrice()
    {
        return Mage::getModel('Saas_UnitPrice_Model_Unitprice');
    }
}

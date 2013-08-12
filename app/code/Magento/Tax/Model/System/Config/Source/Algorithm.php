<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tax_Model_System_Config_Source_Algorithm
{
    protected $_options;

    public function __construct()
    {
        $this->_options = array(
            array(
                'value' => Magento_Tax_Model_Calculation::CALC_UNIT_BASE,
                'label' => Mage::helper('Magento_Tax_Helper_Data')->__('Unit Price')
            ),
            array(
                'value' => Magento_Tax_Model_Calculation::CALC_ROW_BASE,
                'label' => Mage::helper('Magento_Tax_Helper_Data')->__('Row Total')
            ),
            array(
                'value' => Magento_Tax_Model_Calculation::CALC_TOTAL_BASE,
                'label' => Mage::helper('Magento_Tax_Helper_Data')->__('Total')
            ),
        );
    }

    public function toOptionArray()
    {
        return $this->_options;
    }
}

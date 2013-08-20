<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Tax_Model_System_Config_Source_Algorithm
{
    protected $_options;

    public function __construct()
    {
        $this->_options = array(
            array(
                'value' => Mage_Tax_Model_Calculation::CALC_UNIT_BASE,
                'label' => __('Unit Price')
            ),
            array(
                'value' => Mage_Tax_Model_Calculation::CALC_ROW_BASE,
                'label' => __('Row Total')
            ),
            array(
                'value' => Mage_Tax_Model_Calculation::CALC_TOTAL_BASE,
                'label' => __('Total')
            ),
        );
    }

    public function toOptionArray()
    {
        return $this->_options;
    }
}

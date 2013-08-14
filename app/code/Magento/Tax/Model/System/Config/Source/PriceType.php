<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tax_Model_System_Config_Source_PriceType
{
    protected $_options;

    public function __construct()
    {
        $this->_options = array(
            array(
                'value' => 0,
                'label' => Mage::helper('Magento_Tax_Helper_Data')->__('Excluding Tax')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('Magento_Tax_Helper_Data')->__('Including Tax')
            ),
        );
    }

    public function toOptionArray()
    {
        return $this->_options;
    }
}

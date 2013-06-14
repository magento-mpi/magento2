<?php
/**
 * Google AdWords conversation value type source
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleAdwords_Model_Config_Source_ValueType implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * @var Mage_GoogleAdwords_Helper_Data
     */
    protected $_helper;

    /**
     * Constructor
     *
     * @param Mage_GoogleAdwords_Helper_Data $helper
     */
    public function __construct(Mage_GoogleAdwords_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Get conversation value type option
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_GoogleAdwords_Helper_Data::CONVERSION_VALUE_TYPE_DYNAMIC,
                'label' => $this->_helper->__('Dynamic'),
            ),
            array(
                'value' => Mage_GoogleAdwords_Helper_Data::CONVERSION_VALUE_TYPE_CONSTANT,
                'label' => $this->_helper->__('Constant'),
            ),
        );
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Source_Locale_Currency_All implements Magento_Core_Model_Option_ArrayInterface
{
    protected $_options;

    public function toOptionArray($isMultiselect = false)
    {
        if (!$this->_options) {
            $this->_options = Mage::app()->getLocale()->getOptionAllCurrencies();
        }
        $options = $this->_options;
        if (!$isMultiselect) {
            array_unshift($options, array('value'=>'', 'label'=>''));
        }

        return $options;
    }
}

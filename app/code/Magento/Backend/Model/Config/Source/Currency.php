<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Backend_Model_Config_Source_Currency implements Magento_Core_Model_Option_ArrayInterface
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = Mage::app()->getLocale()->getOptionCurrencies();
        }
        $options = $this->_options;
        return $options;
    }
}

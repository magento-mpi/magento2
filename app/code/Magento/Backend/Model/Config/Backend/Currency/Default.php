<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config Directory currency backend model
 * Allows dispatching before and after events for each controller action
 */
class Magento_Backend_Model_Config_Backend_Currency_Default
    extends Magento_Backend_Model_Config_Backend_Currency_Abstract
{
    /**
     * Check default currency is available in installed currencies
     * Check default currency is available in allowed currencies
     *
     * @return Magento_Backend_Model_Config_Backend_Currency_Default
     * @throws Magento_Core_Exception
     */
    protected function _afterSave()
    {
        if (!in_array($this->getValue(), $this->_getInstalledCurrencies())) {
            throw new Magento_Core_Exception(
                __('Sorry, we haven\'t installed the default display currency you selected.')
            );
        }

        if (!in_array($this->getValue(), $this->_getAllowedCurrencies())) {
            throw new Magento_Core_Exception(
                __('Sorry, the default display currency you selected in not available in allowed currencies.')
            );
        }

        return $this;
    }
}

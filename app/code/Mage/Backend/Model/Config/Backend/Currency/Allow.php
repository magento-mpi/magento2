<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Config Directory currency backend model
 *
 * Allows dispatching before and after events for each controller action
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Backend_Currency_Allow extends Mage_Backend_Model_Config_Backend_Currency_Abstract
{
    /**
     * Check is isset default display currency in allowed currencies
     * Check allowed currencies is available in installed currencies
     *
     * @return Mage_Backend_Model_Config_Backend_Currency_Allow
     */
    protected function _afterSave()
    {
        $exceptions = array();
        foreach ($this->_getAllowedCurrencies() as $currencyCode) {
            if (!in_array($currencyCode, $this->_getInstalledCurrencies())) {
                $exceptions[] = __('Selected allowed currency "%s" is not available in installed currencies.',
                    Mage::app()->getLocale()->currency($currencyCode)->getName()
                );
            }
        }

        if (!in_array($this->_getCurrencyDefault(), $this->_getAllowedCurrencies())) {
            $exceptions[] = __('Default display currency "%s" is not available in allowed currencies.',
                Mage::app()->getLocale()->currency($this->_getCurrencyDefault())->getName()
            );
        }

        if ($exceptions) {
            Mage::throwException(join("\n", $exceptions));
        }

        return $this;
    }
}

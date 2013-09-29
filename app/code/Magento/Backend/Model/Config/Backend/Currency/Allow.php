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
 * Config \Directory currency backend model
 *
 * Allows dispatching before and after events for each controller action
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Backend\Currency;

class Allow extends \Magento\Backend\Model\Config\Backend\Currency\AbstractCurrency
{
    /**
     * Check is isset default display currency in allowed currencies
     * Check allowed currencies is available in installed currencies
     *
     * @return \Magento\Backend\Model\Config\Backend\Currency\Allow
     */
    protected function _afterSave()
    {
        $exceptions = array();
        foreach ($this->_getAllowedCurrencies() as $currencyCode) {
            if (!in_array($currencyCode, $this->_getInstalledCurrencies())) {
                $exceptions[] = __('Selected allowed currency "%1" is not available in installed currencies.',
                    \Mage::app()->getLocale()->currency($currencyCode)->getName()
                );
            }
        }

        if (!in_array($this->_getCurrencyDefault(), $this->_getAllowedCurrencies())) {
            $exceptions[] = __('Default display currency "%1" is not available in allowed currencies.',
                \Mage::app()->getLocale()->currency($this->_getCurrencyDefault())->getName()
            );
        }

        if ($exceptions) {
            \Mage::throwException(join("\n", $exceptions));
        }

        return $this;
    }
}

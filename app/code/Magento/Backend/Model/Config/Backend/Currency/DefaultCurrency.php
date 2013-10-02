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
namespace Magento\Backend\Model\Config\Backend\Currency;

class DefaultCurrency
    extends \Magento\Backend\Model\Config\Backend\Currency\AbstractCurrency
{
    /**
     * Check default currency is available in installed currencies
     * Check default currency is available in allowed currencies
     *
     * @return \Magento\Backend\Model\Config\Backend\Currency\DefaultCurrency
     * @throws \Magento\Core\Exception
     */
    protected function _afterSave()
    {
        if (!in_array($this->getValue(), $this->_getInstalledCurrencies())) {
            throw new \Magento\Core\Exception(
                __('Sorry, we haven\'t installed the default display currency you selected.')
            );
        }

        if (!in_array($this->getValue(), $this->_getAllowedCurrencies())) {
            throw new \Magento\Core\Exception(
                __('Sorry, the default display currency you selected in not available in allowed currencies.')
            );
        }

        return $this;
    }
}

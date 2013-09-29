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
 * Backend \Directory currency backend model
 *
 * Allows dispatching before and after events for each controller action
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Backend\Currency;

class Base extends \Magento\Backend\Model\Config\Backend\Currency\AbstractCurrency
{
    /**
     * Check base currency is available in installed currencies
     *
     * @return \Magento\Backend\Model\Config\Backend\Currency\Base
     */
    protected function _afterSave()
    {
        if (!in_array($this->getValue(), $this->_getInstalledCurrencies())) {
            \Mage::throwException(__('Sorry, we haven\'t installed the base currency you selected.'));
        }

        return $this;
    }
}


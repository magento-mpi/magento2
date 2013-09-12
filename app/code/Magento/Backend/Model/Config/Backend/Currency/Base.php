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
 * Backend Directory currency backend model
 *
 * Allows dispatching before and after events for each controller action
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Model_Config_Backend_Currency_Base extends Magento_Backend_Model_Config_Backend_Currency_Abstract
{
    /**
     * Check base currency is available in installed currencies
     *
     * @return Magento_Backend_Model_Config_Backend_Currency_Base
     */
    protected function _afterSave()
    {
        if (!in_array($this->getValue(), $this->_getInstalledCurrencies())) {
            Mage::throwException(__('Sorry, we haven\'t installed the base currency you selected.'));
        }

        return $this;
    }
}


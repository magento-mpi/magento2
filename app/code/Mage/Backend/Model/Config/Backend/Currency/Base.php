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
 * Backend Directory currency backend model
 *
 * Allows dispatching before and after events for each controller action
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Backend_Currency_Base extends Mage_Backend_Model_Config_Backend_Currency_Abstract
{
    /**
     * Check base currency is available in installed currencies
     *
     * @return Mage_Backend_Model_Config_Backend_Currency_Base
     */
    protected function _afterSave()
    {
        if (!in_array($this->getValue(), $this->_getInstalledCurrencies())) {
            Mage::throwException(Mage::helper('Mage_Backend_Helper_Data')
                ->__('Sorry, we haven\'t installed the base currency you selected.'));
        }

        return $this;
    }
}


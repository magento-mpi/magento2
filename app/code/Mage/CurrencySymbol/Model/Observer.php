<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CurrencySymbol
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Currency Symbol Observer
 *
 * @category    Mage
 * @package     Mage_CurrencySymbol
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CurrencySymbol_Model_Observer
{
    /**
     * Generate options for currency displaying with custom currency symbol
     *
     * @param Magento_Event_Observer $observer
     * @return Mage_CurrencySymbol_Model__Observer
     */
    public function currencyDisplayOptions(Magento_Event_Observer $observer)
    {
        $baseCode = $observer->getEvent()->getBaseCode();
        $currencyOptions = $observer->getEvent()->getCurrencyOptions();
        $currencyOptions->setData(Mage::helper('Mage_CurrencySymbol_Helper_Data')->getCurrencyOptions($baseCode));

        return $this;
    }
}

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
 * Currency Symbol helper
 *
 * @category   Mage
 * @package    Mage_CurrencySymbol
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CurrencySymbol_Helper_Data extends Magento_Core_Helper_Data
{

    /**
     * Get currency display options
     *
     * @param string $baseCode
     * @return array
     */
    public function getCurrencyOptions($baseCode)
    {
        $currencyOptions = array();
        $currencySymbol = Mage::getModel('Mage_CurrencySymbol_Model_System_Currencysymbol');
        if($currencySymbol) {
            $customCurrencySymbol = $currencySymbol->getCurrencySymbol($baseCode);

            if ($customCurrencySymbol) {
                $currencyOptions['symbol']  = $customCurrencySymbol;
                $currencyOptions['display'] = Zend_Currency::USE_SYMBOL;
            }
        }

        return $currencyOptions;
    }
}

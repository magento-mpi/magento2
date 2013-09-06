<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Manage currency block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CurrencySymbol_Block_Adminhtml_System_Currency_Rate_Matrix extends Magento_Backend_Block_Template
{

    protected $_template = 'system/currency/rate/matrix.phtml';

    protected function _prepareLayout()
    {
        $newRates = Mage::getSingleton('Magento_Adminhtml_Model_Session')->getRates();
        Mage::getSingleton('Magento_Adminhtml_Model_Session')->unsetData('rates');

        $currencyModel = Mage::getModel('Magento_Directory_Model_Currency');
        $currencies = $currencyModel->getConfigAllowCurrencies();
        $defaultCurrencies = $currencyModel->getConfigBaseCurrencies();
        $oldCurrencies = $this->_prepareRates($currencyModel->getCurrencyRates($defaultCurrencies, $currencies));

        foreach ($currencies as $currency) {
            foreach ($oldCurrencies as $key => $value) {
                if (!array_key_exists($currency, $oldCurrencies[$key])) {
                    $oldCurrencies[$key][$currency] = '';
                }
            }
        }

        foreach ($oldCurrencies as $key => $value) {
            ksort($oldCurrencies[$key]);
        }

        sort($currencies);

        $this->setAllowedCurrencies($currencies)
            ->setDefaultCurrencies($defaultCurrencies)
            ->setOldRates($oldCurrencies)
            ->setNewRates($this->_prepareRates($newRates));

        return parent::_prepareLayout();
    }

    public function getRatesFormAction()
    {
        return $this->getUrl('*/*/saveRates');
    }

    protected function _prepareRates($array)
    {
        if (!is_array($array)) {
            return $array;
        }

        foreach ($array as $key => $rate) {
            foreach ($rate as $code => $value) {
                $parts = explode('.', $value);
                if (sizeof($parts) == 2) {
                    $parts[1] = str_pad(rtrim($parts[1], 0), 4, '0', STR_PAD_RIGHT);
                    $array[$key][$code] = join('.', $parts);
                } elseif ($value > 0) {
                    $array[$key][$code] = number_format($value, 4);
                } else {
                    $array[$key][$code] = null;
                }
            }
        }
        return $array;
    }
}

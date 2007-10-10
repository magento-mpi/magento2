<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Directory
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Currency model
 *
 * @category   Mage
 * @package    Mage_Directory
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Directory_Model_Currency extends Mage_Core_Model_Abstract
{


    /**
     * CONFIG path constants
    */
    const CONFIG_PATH_CURRENCY_ALLOW   = 'currency/options/allow';
    const CONFIG_PATH_CURRENCY_DEFAULT = 'currency/options/default';
    const CONFIG_PATH_CURRENCY_BASE    = 'currency/options/base';

    protected $_filter;

    protected function _construct()
    {
        $this->_init('directory/currency');
    }

    /**
     * Get currency code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getData('currency_code');
    }

    /**
     * Loading currency data
     *
     * @param   string $id
     * @param   string $field
     * @return  Mage_Directory_Model_Currency
     */
    public function load($id, $field=null)
    {
        $this->setData('currency_code', $id);
        return $this;
    }

    /**
     * Get currency rate
     *
     * @param   string $toCurrency
     * @return  double
     */
    public function getRate($toCurrency)
    {
        return $this->getResource()->getRate($this->getCode(), $toCurrency);
    }

    /**
     * Convert price to currency format
     *
     * @param   double $price
     * @param   string $toCurrency
     * @return  double
     */
    public function convert($price, $toCurrency=null)
    {
        if (is_null($toCurrency)) {
            return $price;
        }
        elseif ($rate = $this->getRate($toCurrency)) {
            return $price*$rate;
        }
        throw new Exception(__('Undefined rate from "%s-%s"', $this->getCode(), $toCurrency->getCode()));
    }

    /**
     * Get currency filter
     *
     * @return Mage_Directory_Model_Currency_Filter
     */
    public function getFilter()
    {
        if (!$this->_filter) {
            $this->_filter = new Mage_Directory_Model_Currency_Filter($this->getCode());
        }

        return $this->_filter;
    }

    /**
     * Format price to currency format
     *
     * @param   double $price
     * @return  string
     */
    public function format($price)
    {
        $price = round(floatval($price), 2);
        return Mage::app()->getLocale()->currency($this->getCode())->toCurrency($price);
    }

    /**
     * Retrieve allowed currencies according to config
     *
     */
    public function getConfigAllowCurrencies()
    {
        $allowedCurrencies = $this->getResource()->getConfigCurrencies($this, self::CONFIG_PATH_CURRENCY_ALLOW);
        return $allowedCurrencies;
    }

    /**
     * Retrieve default currencies according to config
     *
     */
    public function getConfigDefaultCurrencies()
    {
        $defaultCurrencies = $this->getResource()->getConfigCurrencies($this, self::CONFIG_PATH_CURRENCY_DEFAULT);
        return $defaultCurrencies;
    }


    public function getConfigBaseCurrencies()
    {
        $defaultCurrencies = $this->getResource()->getConfigCurrencies($this, self::CONFIG_PATH_CURRENCY_BASE);
        return $defaultCurrencies;
    }

    /**
     * Retrieve currency rates to other currencies
     *
     * @param string $currency
     * @param array $toCurrencies
     * @return array
     */
    public function getCurrencyRates($currency, $toCurrencies=null)
    {
        $data = $this->getResource()->getCurrencyRates($currency, $toCurrencies);
        return $data;
    }

    /**
     * Save currency rates
     *
     * @param array $rates
     * @return object
     */
    public function saveRates($rates)
    {
        $this->getResource()->saveRates($rates);
        return $this;
    }
}

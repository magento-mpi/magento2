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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Directory
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Directory Currency Resource Model
 *
 * @category    Mage
 * @package     Mage_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Model_Resource_Currency extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Currency rate table
     *
     * @var string
     */
    protected $_currencyRateTable;

    /**
     * Currency rate cache array
     *
     * @var array
     */
    protected static $_rateCache;

    /**
     * Define main and currency rate tables
     *
     */
    protected function _construct()
    {
        $this->_init('directory/currency', 'currency_code');
        $this->_currencyRateTable   = $this->getTable('directory/currency_rate');
    }

    /**
     * Retrieve currency rate (only base=>allowed)
     *
     * @param string $currencyFrom
     * @param string $currencyTo
     * @return float
     */
    public function getRate($currencyFrom, $currencyTo)
    {
        if ($currencyFrom instanceof Mage_Directory_Model_Currency) {
            $currencyFrom = $currencyFrom->getCode();
        }

        if ($currencyTo instanceof Mage_Directory_Model_Currency) {
            $currencyTo = $currencyTo->getCode();
        }

        if ($currencyFrom == $currencyTo) {
            return 1;
        }

        if (!isset(self::$_rateCache[$currencyFrom][$currencyTo])) {
            $read = $this->_getReadAdapter();
            $select = $read->select()
                ->from($this->_currencyRateTable, 'rate')
                ->where('currency_from=?', strtoupper($currencyFrom))
                ->where('currency_to=?', strtoupper($currencyTo));

            self::$_rateCache[$currencyFrom][$currencyTo] = $read->fetchOne($select);
        }

        return self::$_rateCache[$currencyFrom][$currencyTo];
    }

    /**
     * Retrieve currency rate (base=>allowed or allowed=>base)
     *
     * @param string $currencyFrom
     * @param string $currencyTo
     * @return float
     */
    public function getAnyRate($currencyFrom, $currencyTo)
    {
        if ($currencyFrom instanceof Mage_Directory_Model_Currency) {
            $currencyFrom = $currencyFrom->getCode();
        }

        if ($currencyTo instanceof Mage_Directory_Model_Currency) {
            $currencyTo = $currencyTo->getCode();
        }

        if ($currencyFrom == $currencyTo) {
            return 1;
        }

        if (!isset(self::$_rateCache[$currencyFrom][$currencyTo])) {
            $currencyFrom = strtoupper($currencyFrom);
            $currencyTo   = strtoupper($currencyTo);
            $adapter      = $this->_getReadAdapter();

            $select = $adapter->select()
                ->from($this->_currencyRateTable, "rate")
                ->where('currency_from = ?',$currencyFrom)
                ->where('currency_to = ?', $currencyTo);

            $rate = $adapter->fetchOne($select);
            if ($rate === false) {
                $select = $adapter->select()
                    ->from($this->_currencyRateTable, "1/rate")
                    ->where('currency_to = ?', $currencyTo)
                    ->where('currency_from = ?',$currencyFrom);
            }
            self::$_rateCache[$currencyFrom][$currencyTo] = $adapter->fetchOne($select);
        }

        return self::$_rateCache[$currencyFrom][$currencyTo];
    }

    /**
     * Saving currency rates
     *
     * @param array $rates
     */
    public function saveRates($rates)
    {
        if( is_array($rates) && sizeof($rates) > 0 ) {
            $adapter = $this->_getWriteAdapter();
            $data = array();
            foreach ($rates as $currencyCode => $rate) {
                foreach( $rate as $currencyTo => $value ) {
                    $value = abs($value);
                    if( $value == 0 ) {
                        continue;
                    }
                    $data[] = array(
                        'currency_from' => $currencyCode,
                        'currency_to'   => $currencyTo,
                        'rate'          => $value,
                    );
                }
            }
            if ($data) {
                $adapter->insertOnDuplicate($this->_currencyRateTable, $data, array('rate'));
            }
        } else {
            Mage::throwException(Mage::helper('directory')->__('Invalid rates received'));
        }
    }

    /**
     * Retrieve config currency data by config path
     *
     * @param object $model
     * @param string $path
     * @return array
     */
    public function getConfigCurrencies($model, $path)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
                ->from($this->getTable('core/config_data'))
                ->where($adapter->quoteInto(' path = ? ', $path));

        $data = $adapter->fetchAll($select);
        $tmpArray = array();
        foreach( $data as $configRecord ) {
            $tmpArray = array_merge($tmpArray, explode(',', $configRecord['value']));
        }

        sort($tmpArray);

        $data = array_unique($tmpArray);
        return $data;
    }

    /**
     * Retieve currency rates
     *
     * @param string|array $currency
     * @param array $toCurrencies
     * @return array
     */
    public function getCurrencyRates($currency, $toCurrencies = null)
    {
        $rates = array();
        if( is_array($currency) ) {
            foreach( $currency as $code ) {
                $rates[$code] = $this->_getRatesByCode($code, $toCurrencies);
            }
        } else {
            $rates = $this->_getRatesByCode($currency, $toCurrencies);
        }

        return $rates;
    }

    /**
     * Protected method used by getCurrencyRates() method
     *
     * @param string $code
     * @param array $toCurrencies
     * @return array
     */
    protected function _getRatesByCode($code, $toCurrencies = null)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('directory/currency_rate'), array('currency_to', 'rate'))
            ->where($adapter->quoteInto('currency_from = ?', $code))
            ->where($adapter->quoteInto('currency_to IN(?)', $toCurrencies));

        $data = $adapter->fetchAll($select);

        $tmpArray = array();
        foreach( $data as $currencyFrom => $rate ) {
            $tmpArray[$rate['currency_to']] = $rate['rate'];
        }

        return $tmpArray;
    }
}

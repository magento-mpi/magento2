<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @package     Mage_CurrencySymbol
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Custom currency symbol model
 *
 * @category    Mage
 * @package     Mage_CurrencySymbol
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CurrencySymbol_Model_System_Currencysymbol
{
    /**
     * Custom currency symbol properties
     *
     * @var array
     */
    protected $_symbolsData = array();

    /**
     * Store id
     *
     * @var string | null
     */
    protected $_storeId;

    /**
     * Website id
     *
     * @var string | null
     */
    protected $_websiteId;
    /**
     * Cache types which should be invalidated
     *
     * @var array
     */
    protected $_cacheTypes = array(
        'full_page',
        'config',
        'block_html',
        'layout'
    );

    /**
     * Config path to custom currency symbol value
     */
    const XML_PATH_CUSTOM_CURRENCY_SYMBOL = 'currency/options/customsymbol';
    const XML_PATH_ALLOWED_CURRENCIES     = 'currency/options/allow';

    /*
     * Separator used in config in allowed currencies list
     */
    const ALLOWED_CURRENCIES_CONFIG_SEPARATOR = ',';

    /**
     * Config currency section
     */
    const CONFIG_SECTION = 'currency';

    /**
     * Sets store Id
     *
     * @param  $storeId
     * @return Mage_CurrencySymbol_Model_System_Currencysymbol
     */
    public function setStoreId($storeId=null)
    {
        $this->_storeId = $storeId;
        $this->_symbolsData = array();

        return $this;
    }

    /**
     * Sets website Id
     *
     * @param  $websiteId
     * @return Mage_CurrencySymbol_Model_System_Currencysymbol
     */
    public function setWebsiteId($websiteId=null)
    {
        $this->_websiteId = $websiteId;
        $this->_symbolsData = array();

        return $this;
    }

    /**
     * Returns currency symbol properties array based on config values
     *
     * @return array
     */
    public function getCurrencySymbolsData()
    {
        if (!$this->_symbolsData) {
            $this->_symbolsData = array();

            $allowedCurrencies = array_fill_keys(
                explode(
                    self::ALLOWED_CURRENCIES_CONFIG_SEPARATOR,
                    Mage::getStoreConfig(self::XML_PATH_ALLOWED_CURRENCIES, null)
                ),
                array()
            );

            /* @var $storeModel Mage_Adminhtml_Model_System_Store */
            $storeModel = Mage::getSingleton('adminhtml/system_store');
            foreach ($storeModel->getWebsiteCollection() as $website) {
                $websiteShow = false;
                foreach ($storeModel->getGroupCollection() as $group) {
                    if ($group->getWebsiteId() != $website->getId()) {
                        continue;
                    }
                    foreach ($storeModel->getStoreCollection() as $store) {
                        if ($store->getGroupId() != $group->getId()) {
                            continue;
                        }
                        if (!$websiteShow) {
                            $websiteShow = true;
                            $websiteSymbols  = $website->getConfig(self::XML_PATH_ALLOWED_CURRENCIES);
                            $allowedCurrencies = array_merge(
                                $allowedCurrencies,
                                array_fill_keys(
                                    explode(
                                        self::ALLOWED_CURRENCIES_CONFIG_SEPARATOR,
                                        $websiteSymbols
                                    ),
                                    array()
                                )
                            );
                        }
                        $storeSymbols = Mage::getStoreConfig(self::XML_PATH_ALLOWED_CURRENCIES, $store);
                        $allowedCurrencies = array_merge(
                            $allowedCurrencies,
                            array_fill_keys(
                                explode(
                                    self::ALLOWED_CURRENCIES_CONFIG_SEPARATOR,
                                    $storeSymbols
                                ),
                                array()
                            )
                        );
                    }
                }
            }
            ksort($allowedCurrencies);

            $currentSymbols = Mage::getStoreConfig(self::XML_PATH_CUSTOM_CURRENCY_SYMBOL, null);
            try {
                if ($currentSymbols) {
                    $currentSymbols = unserialize($currentSymbols);
                }
            } catch (Exception $e) {
                $currentSymbols = array();
            }
            /** @var $locale Mage_Core_Model_Locale */
            $locale = Mage::app()->getLocale();
            foreach ($allowedCurrencies as $code => $value) {
                if (!$symbol = $locale->getTranslation($code, 'currencysymbol')) {
                    $symbol = $code;
                }
                if (!$name = $locale->getTranslation($code, 'nametocurrency')) {
                    $name = $code;
                }
                $allowedCurrencies[$code] = array(
                    'parentSymbol'  => $symbol,
                    'displayName' => $name
                );

                if (isset($currentSymbols[$code]) && !empty($currentSymbols[$code])) {
                    $allowedCurrencies[$code]['displaySymbol'] = $currentSymbols[$code];
                } else {
                    $allowedCurrencies[$code]['displaySymbol'] = $allowedCurrencies[$code]['parentSymbol'];
                }
                if ($allowedCurrencies[$code]['parentSymbol'] == $allowedCurrencies[$code]['displaySymbol']) {
                    $allowedCurrencies[$code]['inherited'] = true;
                } else {
                    $allowedCurrencies[$code]['inherited'] = false;
                }
            }
            $this->_symbolsData = $allowedCurrencies;
        }
        return $this->_symbolsData;
    }

    /**
     * Saves currency symbol to config
     *
     * @param  $symbols array
     * @return Mage_CurrencySymbol_Model_System_Currencysymbol
     */
    public function setCurrencySymbolsData($symbols=array())
    {
        foreach ($this->getCurrencySymbolsData() as $code => $values) {
            if (isset($symbols[$code])) {
                if ($symbols[$code] == $values['parentSymbol'] || empty($symbols[$code]))
                unset($symbols[$code]);
            }
        }
        if ($symbols) {
            $value = array(
                'options' => array(
                    'fields' => array(
                        'customsymbol' => array(
                            'value' => serialize($symbols)
                        )
                    )
                )
            );
        } else {
            $value = array('options' => array('fields' => array('customsymbol' => array('inherit' => '1'))));
        }

        Mage::getModel('adminhtml/config_data')
            ->setSection(self::CONFIG_SECTION)
            ->setWebsite(null)
            ->setStore(null)
            ->setGroups($value)
            ->save();

        Mage::dispatchEvent('admin_system_config_changed_section_currency_before_reinit',
            array('website' => $this->_websiteId, 'store' => $this->_storeId)
        );

        // reinit configuration
        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();

        $this->clearCache();

        Mage::dispatchEvent('admin_system_config_changed_section_currency',
            array('website' => $this->_websiteId, 'store' => $this->_storeId)
        );

        return $this;
    }

    /**
     * Returns custom currency symbol by currency code
     *
     * @param  $code
     * @return bool|string
     */
    public function getCurrencySymbol($code)
    {
        $customSymbols = Mage::getStoreConfig(self::XML_PATH_CUSTOM_CURRENCY_SYMBOL, null);
        if ($customSymbols) {
            try {
                $customSymbols  = unserialize($customSymbols);
                if (isset($customSymbols[$code])) {
                    return $customSymbols[$code];
                }
            } catch (Exception $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Clear translate cache
     *
     * @return Saas_Translate_Helper_Data
     */
    public function clearCache()
    {
        // clear cache for frontend
        foreach ($this->_cacheTypes as $cacheType) {
            Mage::app()->getCacheInstance()->invalidateType($cacheType);
        }
        return $this;
    }
}

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
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
 * Locale model
 *
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Model_Locale
{
    /**
     * Default locale name
     */
    const DEFAULT_LOCALE    = 'en_US';
    const DEFAULT_TIMEZONE  = 'America/Los_Angeles';
    const DEFAULT_CURRENCY  = 'USD';
    
    /**
     * XML path constants
     */
    const XML_PATH_DEFAULT_LOCALE   = 'general/locale/code';
    const XML_PATH_DEFAULT_TIMEZONE = 'general/locale/timezone';
    const XML_PATH_DEFAULT_CURRENCY = 'general/locale/currency';
    const XML_PATH_DEFAULT_COUNTRY  = 'general/locale/country';
    const XML_PATH_ALLOW_CODES      = 'global/locale/allow/codes';
    const XML_PATH_ALLOW_CURRENCIES = 'global/locale/allow/currencies';
    
    /**
     * Default locale code
     *
     * @var string
     */
    protected $_defaultLocale;
    
    /**
     * Locale object
     *
     * @var Zend_Locale
     */
    protected $_locale;
    
    public function __construct($locale = null) 
    {
        $this->setLocale($locale);
    }
    
    /**
     * Set default locale code
     *
     * @param   string $locale
     * @return  Mage_Core_Model_Locale
     */
    public function setDefaultLocale($locale)
    {
        $this->_defaultLocale = $locale;
        return $this;
    }
    
    /**
     * REtrieve default locale code
     *
     * @return string
     */
    public function getDefaultLocale()
    {
        if (!$this->_defaultLocale) {
            $this->_defaultLocale = self::DEFAULT_LOCALE;
        }
        return $this->_defaultLocale;
    }
    
    /**
     * Set locale
     *
     * @param   strint $locale
     * @return  Mage_Core_Model_Locale
     */
    public function setLocale($locale = null)
    {
        Mage::dispatchEvent('core_locale_set_locale', array('locale'=>$this));
        
        $locale = Mage::getStoreConfig(self::XML_PATH_DEFAULT_LOCALE);
        if (!$locale) {
            $locale = $this->getDefaultLocale();
        }

        //setlocale(LC_ALL, $locale);
        $this->_locale = new Zend_Locale($locale);
        
        /**
         * @todo retrieve timezone from config
         */
        date_default_timezone_set($this->getTimezone());
        return $this;
    }
    
    /**
     * Retrieve timezone code
     *
     * @return string
     */
    public function getTimezone()
    {
        return self::DEFAULT_TIMEZONE;
    }
    
    /**
     * Retrieve currency code
     *
     * @return string
     */
    public function getCurrency()
    {
        return self::DEFAULT_CURRENCY;
    }
    
    /**
     * Retrieve locale object
     *
     * @return Zend_Locale
     */
    public function getLocale()
    {
        if (!$this->_locale) {
            $this->setLocale();
        }
        
        return $this->_locale;
    }
    
    /**
     * Retrieve locale code
     *
     * @return string
     */
    public function getLocaleCode()
    {
        return $this->getLocale()->toString();
    }
    
    /**
     * Retrieve options array for locale dropdown
     *
     * @return array
     */
    public function getOptionLocales()
    {
        $options    = array();
        $locales    = $this->getLocale()->getLocaleList();
        $languages  = $this->getLocale()->getLanguageTranslationList();
        $countries  = $this->getLocale()->getTranslationList('country');
        
        $allowed    = $this->getAllowLocales();
        foreach ($locales as $code=>$active) {
        	if (strstr($code, '_')) {
        	    if (!in_array($code, $allowed)) {
        	        continue;
        	    }
        	    $data = explode('_', $code);
        	    if (!isset($languages[$data[0]]) || !isset($countries[$data[1]])) {
        	        continue;
        	    }
        	    $options[] = array(
        	       //'label' => ucfirst($languages[$data[0]]) . ' (' . $countries[$data[1]] . ')',
        	       'label' => $languages[$data[0]] . ' (' . $countries[$data[1]] . ')',
        	       'value' => $code,
        	    );
        	}
        }
        return $options;
    }
    
    /**
     * Retrieve timezone option list
     *
     * @return array
     */
    public function getOptionTimezones()
    {
        $options= array();
        $zones  = $this->getLocale()->getTranslationList('timezone');
        ksort($zones);
        foreach ($zones as $code=>$name) {
            $name = trim($name);
    	    $options[] = array(
    	       'label' => empty($name) ? $code : $name . ' (' . $code . ')',
    	       'value' => $code,
    	    );
        }
        return $options;
    }
    
    /**
     * Retrieve country option list
     *
     * @return array
     */
    public function getOptionCountries()
    {
        $options    = array();
        $countries  = $this->getLocale()->getTranslationList('country');
        
        foreach ($countries as $code=>$name) {
    	    $options[] = array(
    	       'label' => $name,
    	       'value' => $code,
    	    );
        }
        return $options;
    }
    
    /**
     * Retrieve currency option list
     *
     * @return unknown
     */
    public function getOptionCurrencies()
    {
        $currencies = $this->getLocale()->getTranslationList('currency');
        $options = array();
        $allowed = $this->getAllowCurrencies();
        foreach ($currencies as $code=>$name) {
            if (!in_array($code, $allowed)) {
                continue;
            }
            /*if (strstr($name, '(')) {
                continue;
            }*/
    	    $options[] = array(
    	       'label' => $name,
    	       'value' => $code,
    	    );
        }
        return $options;
    }
    
    public function getAllowLocales()
    {
        $data = Mage::getConfig()->getNode(self::XML_PATH_ALLOW_CODES)->asArray();
        if ($data) {
            return array_keys($data);
        }
        return array();
    }
    
    public function getAllowCurrencies()
    {
        $data = Mage::getConfig()->getNode(self::XML_PATH_ALLOW_CURRENCIES)->asArray();
        if ($data) {
            return array_keys($data);
        }
        return $data;
    }
}

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
    
    const XML_PATH_DEFAULT_LOCALE   = 'general/local/locale';
    const XML_PATH_DEFAULT_TIMEZONE = 'general/local/timezone';
    const XML_PATH_DEFAULT_CURRENCY = 'general/currency/default';
    
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
    
    public function setDefaultLocale($locale)
    {
        $this->_defaultLocale = $locale;
        return $this;
    }
    
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
        
        $locale = Mage::getStoreConfig('locale');
        if (!$locale) {
            $locale = $this->getDefaultLocale();
        }
        $this->_locale = new Zend_Locale($locale);
        
        /**
         * @todo retrieve timezone from config
         */
        date_default_timezone_set($this->getTimezone());
        return $this;
    }
    
    public function getTimezone()
    {
        return self::DEFAULT_TIMEZONE;
    }
    
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
}
